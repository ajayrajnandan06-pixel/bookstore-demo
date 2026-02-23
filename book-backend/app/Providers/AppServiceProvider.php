<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Models\Order;
use App\Models\Book;
use App\Observers\OrderObserver;
use App\Observers\BookObserver; // Create this too

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix for older MySQL versions
        Schema::defaultStringLength(191);

        // Register observers for automatic cache clearing
        Order::observe(OrderObserver::class);
        
        // Optional: Create BookObserver too if books affect dashboard
        // Book::observe(BookObserver::class);
    }
}
