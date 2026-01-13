@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome to Bookstore Admin Panel')

@section('content')
<div class="row">
    <!-- Stats Cards -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card card-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-icon">
                            <i class="fas fa-book"></i>
                        </div>
                        <h5 class="card-title mb-2">Total Books</h5>
                        <h2 class="mb-0">{{ $stats['total_books'] ?? 0 }}</h2>
                        <small>In inventory</small>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-transparent border-0">
                <a href="{{ route('books.index') }}" class="text-white stretched-link">View details</a>
                <div class="text-white"><i class="fas fa-arrow-right"></i></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card card-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h5 class="card-title mb-2">Total Orders</h5>
                        <h2 class="mb-0">{{ $stats['total_orders'] ?? 0 }}</h2>
                        <small>All time orders</small>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-transparent border-0">
                <a href="{{ route('orders.index') }}" class="text-white stretched-link">View details</a>
                <div class="text-white"><i class="fas fa-arrow-right"></i></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card card-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h5 class="card-title mb-2">Low Stock</h5>
                        <h2 class="mb-0">{{ $stats['low_stock'] ?? 0 }}</h2>
                        <small>Books need restock</small>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-transparent border-0">
                <a href="{{ route('books.index') }}" class="text-white stretched-link">View details</a>
                <div class="text-white"><i class="fas fa-arrow-right"></i></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card dashboard-card card-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="card-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <h5 class="card-title mb-2">Revenue</h5>
                        <h2 class="mb-0">${{ number_format($stats['revenue'] ?? 0, 2) }}</h2>
                        <small>Total revenue</small>
                    </div>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between bg-transparent border-0">
                <a href="{{ route('orders.index') }}" class="text-white stretched-link">View details</a>
                <div class="text-white"><i class="fas fa-arrow-right"></i></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-xl-8 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Recent Orders</h5>
            </div>
            <div class="card-body">
                @if(isset($recent_orders) && $recent_orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                    <tr>
                                        <td><code>{{ $order->order_number ?? 'N/A' }}</code></td>
                                        <td>
                                            @if(isset($order->customer) && $order->customer)
                                                {{ $order->customer->name }}
                                            @else
                                                <span class="text-muted">Customer not found</span>
                                            @endif
                                        </td>
                                        <td>{{ $order->created_at->format('M d, Y') ?? 'N/A' }}</td>
                                        <td>${{ isset($order->total) ? number_format($order->total, 2) : '0.00' }}</td>
                                        <td>
                                            @if(isset($order->status))
                                                @if($order->status == 'completed')
                                                    <span class="badge bg-success">{{ $order->status }}</span>
                                                @elseif($order->status == 'processing')
                                                    <span class="badge bg-warning">{{ $order->status }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $order->status }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-secondary">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($order->payment_status))
                                                @if($order->payment_status == 'paid')
                                                    <span class="badge bg-success">{{ $order->payment_status }}</span>
                                                @elseif($order->payment_status == 'failed')
                                                    <span class="badge bg-danger">{{ $order->payment_status }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $order->payment_status }}</span>
                                                @endif
                                            @else
                                                <span class="badge bg-warning">N/A</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">View All Orders</a>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                        <h5>No recent orders</h5>
                        <p class="text-muted">Orders will appear here once created.</p>
                        <a href="{{ route('orders.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Order
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="col-xl-4 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('books.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>Add New Book
                    </a>
                    <a href="{{ route('orders.create') }}" class="btn btn-success btn-lg">
                        <i class="fas fa-shopping-cart me-2"></i>Create New Order
                    </a>
                    <a href="{{ route('customers.create') }}" class="btn btn-warning btn-lg">
                        <i class="fas fa-user-plus me-2"></i>Add Customer
                    </a>
                    <a href="{{ route('books.index') }}" class="btn btn-info btn-lg">
                        <i class="fas fa-chart-bar me-2"></i>View Inventory
                    </a>
                </div>
                
                <hr>
                
                <h6 class="mb-3">System Status</h6>
                <div class="list-group">
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Database
                        <span class="badge bg-success rounded-pill">Connected</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Server
                        <span class="badge bg-success rounded-pill">Online</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        Last Backup
                        <span class="badge bg-warning rounded-pill">Today</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Books -->
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-book me-2"></i>Recent Books Added</h5>
                <a href="{{ route('books.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if(isset($recent_books) && $recent_books->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>ISBN</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Added On</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_books as $book)
                                    <tr>
                                        <td>
                                            <strong>{{ $book->title }}</strong>
                                            @if($book->category)
                                                <small class="d-block text-muted">{{ $book->category }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $book->author }}</td>
                                        <td><code>{{ $book->isbn }}</code></td>
                                        <td>${{ number_format($book->price, 2) }}</td>
                                        <td>
                                            @if($book->quantity > 10)
                                                <span class="badge bg-success">{{ $book->quantity }}</span>
                                            @elseif($book->quantity > 0)
                                                <span class="badge bg-warning">{{ $book->quantity }}</span>
                                            @else
                                                <span class="badge bg-danger">0</span>
                                            @endif
                                        </td>
                                        <td>{{ $book->created_at->format('M d, Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-book fa-2x text-muted mb-3"></i>
                        <h5>No books added yet</h5>
                        <p class="text-muted">Add your first book to see it here.</p>
                        <a href="{{ route('books.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add First Book
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 5000);
    });
</script>
@endsection