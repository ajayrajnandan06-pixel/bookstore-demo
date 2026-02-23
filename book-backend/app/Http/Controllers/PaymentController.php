<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    /**
     * Show payment page - OPTIMIZED
     */
    public function create(Order $order)
    {
        $order->load([
            'customer:id,name,email,phone',
            'items:id,order_id,book_id,quantity,price,total',
            'items.book:id,title',
            'payments:id,order_id,amount,status,method,created_at'
        ]);
        
        if ($order->isFullyPaid()) {
            return redirect()
                ->route('orders.show', $order)
                ->with('info', 'Order is already fully paid.');
        }
        
        return view('payments.create', compact('order'));
    }

    /**
     * Process payment - OPTIMIZED
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'method' => 'required|in:cash,card,online,bank_transfer',
            'amount' => 'required|numeric|min:0.01|max:' . $order->due_amount,
            'gateway' => 'nullable|required_if:method,online|in:razorpay',
            'notes' => 'nullable|string'
        ]);

        try {
            return DB::transaction(function () use ($order, $request) {
                switch ($request->method) {
                    case 'online':
                        return $this->processOnlinePayment($order, $request);
                    case 'card':
                        return $this->processCardPayment($order, $request);
                    case 'bank_transfer':
                        return $this->processBankTransfer($order, $request);
                    default:
                        return $this->processCashPayment($order, $request);
                }
            });
        } catch (\Exception $e) {
            Log::error('Payment processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->with('error', 'Payment failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process cash payment - OPTIMIZED
     */
    private function processCashPayment($order, $request)
    {
        $payment = $order->recordPayment([
            'amount' => $request->amount,
            'method' => 'cash',
            'status' => 'completed',
            'paid_at' => now(),
            'notes' => $request->notes
        ]);

        // Clear dashboard cache
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.recent_orders');

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Cash payment recorded successfully!');
    }

    /**
     * Process card payment - OPTIMIZED
     */
    private function processCardPayment($order, $request)
    {
        $payment = $order->recordPayment([
            'amount' => $request->amount,
            'method' => 'card',
            'status' => 'completed',
            'paid_at' => now(),
            'notes' => ($request->notes ?? '') . ' | Manual card payment'
        ]);

        Cache::forget('dashboard.stats');

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Card payment recorded successfully!');
    }

    /**
     * Process bank transfer - OPTIMIZED
     */
    private function processBankTransfer($order, $request)
    {
        $payment = $order->recordPayment([
            'amount' => $request->amount,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'notes' => ($request->notes ?? '') . ' | Bank transfer - pending verification'
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('warning', 'Bank transfer recorded. Status: Pending verification.');
    }

    /**
     * Process online payment - OPTIMIZED
     */
    private function processOnlinePayment($order, $request)
    {
        if ($request->gateway === 'razorpay') {
            return $this->initiateRazorpayPayment($order, $request);
        }
        
        return back()->with('error', 'Invalid payment gateway selected.');
    }

    /**
     * Create Razorpay order - OPTIMIZED with atomic operations
     */
    public function createRazorpayOrder(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $order->due_amount
        ]);

        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            // Create Razorpay order with idempotency
            $receiptId = $order->order_number . '_' . time() . '_' . Str::random(4);
            
            $razorpayOrder = $api->order->create([
                'receipt' => $receiptId,
                'amount' => $request->amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1,
                'notes' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]
            ]);

            // OPTIMIZATION: Create payment record in single transaction
            $payment = DB::transaction(function () use ($order, $request, $razorpayOrder) {
                return Payment::create([
                    'order_id' => $order->id,
                    'amount' => $request->amount,
                    'method' => 'online',
                    'gateway' => 'razorpay',
                    'transaction_id' => $razorpayOrder->id,
                    'status' => 'pending',
                    'gateway_response' => $razorpayOrder->toArray(),
                    'notes' => $request->notes ?? null
                ]);
            });

            return response()->json([
                'success' => true,
                'order_id' => $razorpayOrder->id,
                'amount' => $razorpayOrder->amount,
                'currency' => $razorpayOrder->currency,
                'payment_id' => $payment->id,
                'razorpay_key' => config('services.razorpay.key'),
                'customer_name' => $order->customer->name,
                'customer_email' => $order->customer->email,
                'customer_phone' => $order->customer->phone
            ]);

        } catch (\Exception $e) {
            Log::error('Razorpay order creation failed', [
                'error' => $e->getMessage(),
                'order_id' => $order->id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create Razorpay order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify Razorpay payment - OPTIMIZED with locking
     */
    public function verifyRazorpayPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'payment_id' => 'required'
        ]);

        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            // Verify signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $api->utility->verifyPaymentSignature($attributes);

            // OPTIMIZATION: Lock payment record to prevent double-processing
            return DB::transaction(function () use ($request) {
                $payment = Payment::where('id', $request->payment_id)
                    ->where('status', 'pending') // Only process pending payments
                    ->lockForUpdate()
                    ->firstOrFail();
                
                $payment->update([
                    'status' => 'completed',
                    'gateway_transaction_id' => $request->razorpay_payment_id,
                    'paid_at' => now(),
                    'gateway_response' => array_merge(
                        $payment->gateway_response ?? [],
                        [
                            'payment_id' => $request->razorpay_payment_id,
                            'signature_verified' => true,
                            'verified_at' => now()->toDateTimeString()
                        ]
                    )
                ]);

                // Update order payment status
                $payment->order->updatePaymentStatus();
                
                // Clear caches
                Cache::forget('dashboard.stats');
                Cache::forget('dashboard.recent_orders');

                return response()->json([
                    'success' => true,
                    'message' => 'Payment verified successfully!',
                    'redirect_url' => route('orders.show', $payment->order)
                ]);
            });

        } catch (\Exception $e) {
            Log::error('Razorpay payment verification failed', [
                'error' => $e->getMessage(),
                'payment_id' => $request->payment_id ?? null
            ]);

            // Mark payment as failed only if it exists
            if (isset($request->payment_id)) {
                Payment::where('id', $request->payment_id)->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Initiate Razorpay payment - OPTIMIZED
     */
    private function initiateRazorpayPayment($order, $request)
    {
        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            $receiptId = $order->order_number . '_' . time();
            
            $razorpayOrder = $api->order->create([
                'receipt' => $receiptId,
                'amount' => $request->amount * 100,
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            $payment = $order->recordPayment([
                'amount' => $request->amount,
                'method' => 'online',
                'gateway' => 'razorpay',
                'transaction_id' => $razorpayOrder->id,
                'status' => 'pending',
                'gateway_response' => $razorpayOrder->toArray(),
                'notes' => $request->notes
            ]);

            return view('payments.razorpay', compact('order', 'payment', 'razorpayOrder'));
            
        } catch (\Exception $e) {
            throw new \Exception('Razorpay error: ' . $e->getMessage());
        }
    }

    /**
     * Process refund - OPTIMIZED
     */
    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|max:500'
        ]);

        try {
            return DB::transaction(function () use ($payment, $request) {
                // Handle refund based on gateway
                if ($payment->gateway === 'razorpay') {
                    $this->processRazorpayRefund($payment, $request);
                } else {
                    $this->processManualRefund($payment, $request);
                }
                
                // Clear caches
                Cache::forget('dashboard.stats');
                Cache::forget('dashboard.recent_orders');

                return redirect()
                    ->route('orders.show', $payment->order)
                    ->with('success', 'Refund processed successfully!');
            });
                
        } catch (\Exception $e) {
            Log::error('Refund processing failed', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->with('error', 'Refund failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Process Razorpay refund - OPTIMIZED
     */
    private function processRazorpayRefund($payment, $request)
    {
        $api = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );

        $refund = $api->payment->fetch($payment->gateway_transaction_id)->refund([
            'amount' => $request->amount * 100,
            'notes' => ['reason' => $request->reason]
        ]);

        // Update payment status
        $payment->update([
            'status' => $request->amount == $payment->amount ? 'refunded' : 'partially_refunded'
        ]);

        // Create refund record
        $payment->order->recordPayment([
            'amount' => -$request->amount,
            'method' => $payment->method,
            'gateway' => $payment->gateway,
            'transaction_id' => 'refund_' . $refund->id,
            'gateway_transaction_id' => $refund->id,
            'status' => 'completed',
            'paid_at' => now(),
            'gateway_response' => $refund->toArray(),
            'notes' => 'Refund: ' . $request->reason
        ]);

        $payment->order->updatePaymentStatus();
    }

    /**
     * Process manual refund - OPTIMIZED
     */
    private function processManualRefund($payment, $request)
    {
        $payment->update([
            'status' => $request->amount == $payment->amount ? 'refunded' : 'partially_refunded'
        ]);

        $payment->order->recordPayment([
            'amount' => -$request->amount,
            'method' => $payment->method,
            'gateway' => $payment->gateway,
            'status' => 'completed',
            'paid_at' => now(),
            'notes' => 'Manual Refund: ' . $request->reason
        ]);

        $payment->order->updatePaymentStatus();
    }

    /**
     * Verify bank transfer - OPTIMIZED
     */
    public function verifyBankTransfer(Payment $payment)
    {
        if ($payment->method !== 'bank_transfer' || $payment->status !== 'pending') {
            return back()->with('error', 'Invalid payment for verification.');
        }

        DB::transaction(function () use ($payment) {
            $payment->update([
                'status' => 'completed',
                'paid_at' => now(),
                'notes' => $payment->notes . ' | Verified on ' . now()->format('Y-m-d H:i')
            ]);

            $payment->order->updatePaymentStatus();
            
            Cache::forget('dashboard.stats');
        });

        return back()->with('success', 'Bank transfer verified successfully!');
    }
}
