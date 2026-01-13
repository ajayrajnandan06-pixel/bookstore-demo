<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     */
    public function index()
    {
        // Get real stats from database
        $stats = [
            'total_books' => Book::count(),
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
            'revenue' => Order::where('payment_status', 'paid')->sum('total'),
            'low_stock' => Book::where('quantity', '<', 10)->where('quantity', '>', 0)->count(),
        ];
        
        // Get recent books (last 5)
        $recent_books = Book::latest()->take(5)->get();
        
        // Get recent orders (last 5)
        $recent_orders = Order::with('customer')->latest()->take(5)->get();
        
        return view('dashboard', compact('stats', 'recent_books', 'recent_orders'));
    }
}