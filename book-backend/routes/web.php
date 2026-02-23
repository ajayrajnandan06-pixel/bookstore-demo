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

    // Payment routes are auth-protected for client environments.
    Route::prefix('payments')->name('payments.')->group(function () {
    // Create payment page
    Route::get('/order/{order}/create', [PaymentController::class, 'create'])->name('create');
    
    // Process payment
    Route::post('/order/{order}', [PaymentController::class, 'store'])->name('store');

    Route::post('/razorpay/verify', [PaymentController::class, 'verifyRazorpayPayment'])
        ->name('razorpay.verify');
    
    // Refund routes
    Route::get('/{payment}/refund', [PaymentController::class, 'showRefundForm'])->name('refund.create');
    Route::post('/{payment}/refund', [PaymentController::class, 'refund'])->name('refund');
    
    // Bank transfer verification
    Route::post('/{payment}/verify-bank-transfer', [PaymentController::class, 'verifyBankTransfer'])->name('verify.bank');
    });

    // AJAX payment endpoints
    Route::prefix('api/payments')->name('payments.api.')->group(function () {
        Route::post('/razorpay/{order}/create-order', [PaymentController::class, 'createRazorpayOrder'])
            ->name('razorpay.create-order');
    });
});

// Development-only routes
if (app()->environment('local')) {
    Route::get('/test', function () {
        return 'Laravel is working!';
    });

    Route::middleware('auth')->prefix('razorpay')->name('razorpay.')->group(function () {
        Route::get('/test/order/{order}', [TestRazorpayController::class, 'testPayment'])->name('test');
        Route::post('/test/{order}/create-order', [TestRazorpayController::class, 'createOrder'])
            ->name('create-order');
        Route::post('/verify-payment', [TestRazorpayController::class, 'verifyPayment'])->name('verify');
        Route::get('/success', [TestRazorpayController::class, 'success'])->name('success');
        Route::get('/failure', [TestRazorpayController::class, 'failure'])->name('failure');
        Route::get('/test-cards', [TestRazorpayController::class, 'testCards'])->name('test-cards');
    });
}

// Debug route intentionally disabled for client-shared environments.
// It exposes payment gateway configuration state and should not be public.
// Route::get('/debug-razorpay', function() {
//     $api = new Razorpay\Api\Api(
//         config('services.razorpay.key'),
//         config('services.razorpay.secret')
//     );
//
//     try {
//         // Test API connection
//         $orders = $api->order->all(['count' => 1]);
//         return response()->json([
//             'success' => true,
//             'message' => 'API connected successfully',
//             'orders' => $orders->items
//         ]);
//     } catch (\Exception $e) {
//         return response()->json([
//             'success' => false,
//             'error' => $e->getMessage(),
//             'key' => config('services.razorpay.key') ? 'Set' : 'Not set',
//             'secret' => config('services.razorpay.secret') ? 'Set' : 'Not set'
//         ], 500);
//     }
// });

// Setup routes intentionally disabled for client-shared environments.
// They run privileged artisan commands and should never be publicly accessible.
// Route::get('/__setup-now', function () {
//     Artisan::call('optimize:clear');
//     Artisan::call('migrate --force');
//     Artisan::call('db:seed');
//     return 'Migration & seeding done';
// });
//
// Route::get('/__clear', function () {
//     Artisan::call('optimize:clear');
//     return 'Config cleared';
// });
