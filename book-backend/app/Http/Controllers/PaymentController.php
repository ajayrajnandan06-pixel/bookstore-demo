<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Exception;

class PaymentController extends Controller
{
    /**
     * Show payment page for order
     */
    public function create(Order $order)
    {
        $order->load('customer', 'items.book', 'payments');
        
        // Check if order is already paid
        if ($order->isFullyPaid()) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Order is already fully paid.');
        }
        
        return view('payments.create', compact('order'));
    }

    /**
     * Process payment
     */
    public function store(Request $request, Order $order)
    {
        $request->validate([
            'method' => 'required|in:cash,card,online,bank_transfer',
            'amount' => 'required|numeric|min:0.01|max:' . $order->due_amount,
            'gateway' => 'nullable|required_if:method,online|in:razorpay', // Removed stripe, paypal
            'notes' => 'nullable|string'
        ]);

        DB::beginTransaction();

        try {
            // Handle different payment methods
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
            
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Payment failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Process cash payment
     */
    private function processCashPayment($order, $request)
    {
        // Create cash payment record
        $payment = $order->recordPayment([
            'amount' => $request->amount,
            'method' => 'cash',
            'status' => 'completed',
            'notes' => $request->notes
        ]);

        DB::commit();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Cash payment recorded successfully!');
    }

    /**
     * Process card payment (manual entry for now)
     */
    private function processCardPayment($order, $request)
    {
        // For manual card entry (like via terminal)
        $payment = $order->recordPayment([
            'amount' => $request->amount,
            'method' => 'card',
            'status' => 'completed',
            'notes' => $request->notes . ' | Manual card payment'
        ]);

        DB::commit();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Card payment recorded successfully!');
    }

    /**
     * Process bank transfer
     */
    private function processBankTransfer($order, $request)
    {
        // Bank transfer - mark as pending until verified
        $payment = $order->recordPayment([
            'amount' => $request->amount,
            'method' => 'bank_transfer',
            'status' => 'pending',
            'notes' => $request->notes . ' | Bank transfer - pending verification'
        ]);

        DB::commit();

        return redirect()->route('orders.show', $order)
            ->with('warning', 'Bank transfer recorded. Status: Pending verification.');
    }

    /**
     * Process online payment - show gateway options
     */
    private function processOnlinePayment($order, $request)
    {
        $gateway = $request->gateway;
        
        // Only Razorpay remains
        switch ($gateway) {
            case 'razorpay':
                return $this->initiateRazorpayPayment($order, $request);
            default:
                return back()->with('error', 'Invalid payment gateway selected.');
        }
    }

    /**
     * Create Razorpay order (API Endpoint)
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

            // Create Razorpay order
            $razorpayOrder = $api->order->create([
                'receipt' => $order->order_number . '_' . time(),
                'amount' => $request->amount * 100, // Convert to paise
                'currency' => 'INR',
                'payment_capture' => 1,
                'notes' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number
                ]
            ]);

            // Create pending payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $request->amount,
                'method' => 'online',
                'gateway' => 'razorpay',
                'transaction_id' => $razorpayOrder->id,
                'status' => 'pending',
                'gateway_response' => $razorpayOrder->toArray(),
                'notes' => $request->notes ?? null
            ]);

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
     * Verify Razorpay payment
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

            DB::beginTransaction();

            // Find and update payment
            $payment = Payment::findOrFail($request->payment_id);
            
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

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully!',
                'redirect_url' => route('orders.show', $payment->order)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Mark payment as failed
            if (isset($payment)) {
                $payment->update(['status' => 'failed']);
            }

            Log::error('Razorpay payment verification failed', [
                'error' => $e->getMessage(),
                'payment_id' => $request->payment_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Initiate Razorpay payment
     */
    private function initiateRazorpayPayment($order, $request)
    {
        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            // Create Razorpay order
            $razorpayOrder = $api->order->create([
                'receipt' => $order->order_number,
                'amount' => $request->amount * 100, // Convert to paise
                'currency' => 'INR',
                'payment_capture' => 1
            ]);

            // Create pending payment record
            $payment = $order->recordPayment([
                'amount' => $request->amount,
                'method' => 'online',
                'gateway' => 'razorpay',
                'transaction_id' => $razorpayOrder->id,
                'status' => 'pending',
                'gateway_response' => $razorpayOrder->toArray(),
                'notes' => $request->notes
            ]);

            DB::commit();

            return view('payments.razorpay', compact('order', 'payment', 'razorpayOrder'));
            
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Razorpay error: ' . $e->getMessage());
        }
    }

    /**
     * Show refund form
     */
    public function showRefundForm(Payment $payment)
    {
        if (!$payment->isRefundable()) {
            return back()->with('error', 'This payment cannot be refunded.');
        }

        return view('payments.refund', compact('payment'));
    }

    /**
     * Process refund
     */
    public function refund(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $payment->amount,
            'reason' => 'required|string|max:500'
        ]);

        DB::beginTransaction();

        try {
            // Handle refund based on gateway
            if ($payment->gateway === 'razorpay') {
                $this->processRazorpayRefund($payment, $request);
            } else {
                // Manual refund for cash/bank/other
                $this->processManualRefund($payment, $request);
            }

            DB::commit();
            
            return redirect()->route('orders.show', $payment->order)
                ->with('success', 'Refund processed successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Refund failed: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Process Razorpay refund
     */
    private function processRazorpayRefund($payment, $request)
    {
        try {
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            // Process refund
            $refund = $api->payment->fetch($payment->gateway_transaction_id)->refund([
                'amount' => $request->amount * 100,
                'notes' => ['reason' => $request->reason]
            ]);

            // Update original payment if full refund
            if ($request->amount == $payment->amount) {
                $payment->update(['status' => 'refunded']);
            } else {
                $payment->update(['status' => 'partially_refunded']);
            }

            // Create refund record
            $payment->order->recordPayment([
                'amount' => -$request->amount,
                'method' => $payment->method,
                'gateway' => $payment->gateway,
                'transaction_id' => 'refund_' . $refund->id,
                'gateway_transaction_id' => $refund->id,
                'status' => 'completed',
                'gateway_response' => $refund->toArray(),
                'notes' => 'Refund: ' . $request->reason
            ]);

            // Update order payment status
            $payment->order->updatePaymentStatus();

        } catch (\Exception $e) {
            throw new \Exception('Razorpay refund failed: ' . $e->getMessage());
        }
    }

    /**
     * Process manual refund
     */
    private function processManualRefund($payment, $request)
    {
        // Update original payment status
        if ($request->amount == $payment->amount) {
            $payment->update(['status' => 'refunded']);
        } else {
            $payment->update(['status' => 'partially_refunded']);
        }

        // Create refund record
        $payment->order->recordPayment([
            'amount' => -$request->amount, // Negative amount for refund
            'method' => $payment->method,
            'gateway' => $payment->gateway,
            'status' => 'completed',
            'notes' => 'Manual Refund: ' . $request->reason
        ]);

        // Update order payment status
        $payment->order->updatePaymentStatus();
    }

    /**
     * Mark bank transfer as verified
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
        });

        return back()->with('success', 'Bank transfer verified successfully!');
    }
}