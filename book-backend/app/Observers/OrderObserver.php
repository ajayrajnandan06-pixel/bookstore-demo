<?php

namespace App\Observers;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class OrderObserver
{
    /**
     * Handle order "created" event.
     */
    public function created(Order $order): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle order "updated" event.
     */
    public function updated(Order $order): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle order "restored" event.
     */
    public function restored(Order $order): void
    {
        $this->clearDashboardCache();
    }

    /**
     * Handle order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        $this->clearDashboardCache();
    }

    private function clearDashboardCache(): void
    {
        Cache::forget('dashboard.stats');
        Cache::forget('dashboard.recent_orders');
        Cache::forget('dashboard.top_books_month');
    }
}
