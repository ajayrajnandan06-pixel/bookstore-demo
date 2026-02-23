<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CustomerController;

/*
|--------------------------------------------------------------------------
| Internal API Routes (AJAX Only - Still Uses Session Auth)
|--------------------------------------------------------------------------
|
| These routes are for AJAX requests ONLY. They still use session-based
| authentication (NOT JWT). DO NOT use these for external APIs.
|
*/

Route::middleware(['web', 'auth'])->prefix('internal')->group(function () {
    
    // Dashboard live stats (polling every 30 seconds)
    Route::get('/dashboard/stats', [DashboardController::class, 'liveStats'])
        ->name('api.dashboard.stats');
    
    // Order calculation preview (before submit)
    Route::post('/orders/calculate', [OrderController::class, 'calculateTotal'])
        ->name('api.orders.calculate');
    
    // Search autocomplete endpoints
    Route::get('/books/search', [BookController::class, 'search'])
        ->name('api.books.search');
    
    Route::get('/customers/search', [CustomerController::class, 'search'])
        ->name('api.customers.search');
    
    // Quick order status updates
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])
        ->name('api.orders.update-status');
});