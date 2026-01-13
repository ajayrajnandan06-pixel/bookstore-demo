<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Razorpay\Api\Api;
use Illuminate\Support\Facades\DB;

class TestRazorpayController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            config('services.razorpay.key'),
            config('services.razorpay.secret')
        );
    }

    /**
     * Show test Razorpay payment page
     */
    public function testPayment(Order $order)
    {
        $order->load('customer');
        return view('payments.test-razorpay', compact('order'));
    }

    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request, Order $order)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        try {
            // Convert amount to paise (Indian currency)
            $amount = $request->amount * 100;

            // Create Razorpay Order
            $razorpayOrder = $this->razorpay->order->create([
                'receipt' => 'order_' . $order->id . '_' . time(),
                'amount' => $amount,
                'currency' => 'INR',
                'payment_capture' => 1 // Auto capture
            ]);

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'amount' => $request->amount,
                'method' => 'online',
                'gateway' => 'razorpay',
                'transaction_id' => $razorpayOrder->id,
                'status' => 'pending',
                'gateway_response' => $razorpayOrder->toArray()
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $razorpayOrder->id,
                'amount' => $amount,
                'currency' => 'INR',
                'key' => config('services.razorpay.key'),
                'payment_id' => $payment->id,
                'customer_name' => $order->customer->name,
                'customer_email' => $order->customer->email,
                'customer_phone' => $order->customer->phone
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify payment
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
            'payment_id' => 'required'
        ]);

        try {
            // Verify signature
            $attributes = [
                'razorpay_order_id' => $request->razorpay_order_id,
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature
            ];

            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Find and update payment
            $payment = Payment::findOrFail($request->payment_id);
            
            DB::transaction(function () use ($payment, $request) {
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
            });

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully!',
                'redirect_url' => route('orders.show', $payment->order)
            ]);

        } catch (\Exception $e) {
            // Update payment as failed
            if (isset($payment)) {
                $payment->update(['status' => 'failed']);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Test payment success page
     */
    public function success(Request $request)
    {
        $paymentId = $request->get('payment_id');
        $orderId = $request->get('order_id');
        
        return view('payments.razorpay-success', compact('paymentId', 'orderId'));
    }

    /**
     * Test payment failure page
     */
    public function failure(Request $request)
    {
        $error = $request->get('error');
        return view('payments.razorpay-failure', compact('error'));
    }

    /**
     * Get test card details
     */
    public function testCards()
    {
        $testCards = [
            [
                'number' => '4111 1111 1111 1111',
                'name' => 'Successful Payment',
                'description' => 'Always results in a successful payment',
                'expiry' => '12/30',
                'cvv' => '123'
            ],
            [
                'number' => '4111 1111 1111 1112',
                'name' => 'Failure Payment',
                'description' => 'Always results in a failed payment',
                'expiry' => '12/30',
                'cvv' => '123'
            ],
            [
                'number' => '5104 0600 0000 0008',
                'name' => 'Success MasterCard',
                'description' => 'MasterCard test card for successful payments',
                'expiry' => '12/30',
                'cvv' => '123'
            ]
        ];

        return response()->json($testCards);
    }
}