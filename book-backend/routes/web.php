<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TestRazorpayController;

// Public Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Resources
    Route::resources([
        'books' => BookController::class,
        'customers' => CustomerController::class,
        'orders' => OrderController::class,
    ]);
    
    // Order Quick Actions
    Route::prefix('orders')->group(function () {
        Route::patch('/{order}/mark-completed', [OrderController::class, 'markAsCompleted'])->name('orders.mark-completed');
        Route::patch('/{order}/mark-paid', [OrderController::class, 'markAsPaid'])->name('orders.mark-paid');
        Route::patch('/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('/{order}/invoice/download', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.download');
    });
});

// Test route
Route::get('/test', function () {
    return 'Laravel is working!';
});

// Payment Routes - WITHOUT CSRF FOR SOME ENDPOINTS
Route::prefix('payments')->name('payments.')->group(function () {
    // Create payment page
    Route::get('/order/{order}/create', [PaymentController::class, 'create'])->name('create');
    
    // Process payment
    Route::post('/order/{order}', [PaymentController::class, 'store'])->name('store');
    
    // Remove these Stripe routes:
    // Route::get('/stripe/{order}/{payment}/success', [PaymentController::class, 'stripeSuccess'])->name('stripe.success');
    // Route::get('/stripe/{order}/{payment}/cancel', [PaymentController::class, 'stripeCancel'])->name('stripe.cancel');
    
    // Razorpay payment verification - WITHOUT CSRF
    Route::post('/razorpay/verify', [PaymentController::class, 'verifyRazorpayPayment'])
        ->name('razorpay.verify')
        ->withoutMiddleware(['csrf']);
    
    // Refund routes
    Route::get('/{payment}/refund', [PaymentController::class, 'showRefundForm'])->name('refund.create');
    Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
    
    // Bank transfer verification
    Route::post('/{payment}/verify-bank-transfer', [PaymentController::class, 'verifyBankTransfer'])->name('verify.bank');
});

// API Payment Endpoints - WITHOUT CSRF
Route::prefix('api/payments')->name('payments.api.')->group(function () {
    // REMOVE THIS STRIPE ROUTE:
    // Route::post('/stripe/{order}/create-intent', [PaymentController::class, 'createStripeIntent'])
    //     ->name('stripe.create-intent')
    //     ->withoutMiddleware(['csrf']);
    
    Route::post('/razorpay/{order}/create-order', [PaymentController::class, 'createRazorpayOrder'])
        ->name('razorpay.create-order')
        ->withoutMiddleware(['csrf']);
});

// Razorpay Test Routes
Route::prefix('razorpay')->name('razorpay.')->group(function () {
    Route::get('/test/order/{order}', [TestRazorpayController::class, 'testPayment'])->name('test');
    Route::post('/test/{order}/create-order', [TestRazorpayController::class, 'createOrder'])
        ->name('create-order')
        ->withoutMiddleware(['csrf']);
    
    Route::post('/verify-payment', [TestRazorpayController::class, 'verifyPayment'])
        ->name('verify')
        ->withoutMiddleware(['csrf']);
    
    Route::get('/success', [TestRazorpayController::class, 'success'])->name('success');
    Route::get('/failure', [TestRazorpayController::class, 'failure'])->name('failure');
    Route::get('/test-cards', [TestRazorpayController::class, 'testCards'])->name('test-cards');
});

// Debug route
Route::get('/debug-razorpay', function() {
    $api = new Razorpay\Api\Api(
        config('services.razorpay.key'),
        config('services.razorpay.secret')
    );
    
    try {
        // Test API connection
        $orders = $api->order->all(['count' => 1]);
        return response()->json([
            'success' => true,
            'message' => 'API connected successfully',
            'orders' => $orders->items
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'key' => config('services.razorpay.key') ? 'Set' : 'Not set',
            'secret' => config('services.razorpay.secret') ? 'Set' : 'Not set'
        ], 500);
    }
});