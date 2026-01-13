@extends('layouts.app')

@section('title', 'Manage Orders')
@section('page-title', 'Order Management')
@section('page-subtitle', 'View and manage customer orders')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Orders</h5>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Create New Order
        </a>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($orders->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h4>No orders found</h4>
                <p class="text-muted">Create your first order to get started.</p>
                <a href="{{ route('orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create First Order
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <strong><code>{{ $order->order_number }}</code></strong>
                                </td>
                                <td>
                                    @if($order->customer)
                                        <strong>{{ $order->customer->name }}</strong>
                                        @if($order->customer->email)
                                            <div class="small text-muted">{{ $order->customer->email }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">Customer Deleted</span>
                                    @endif
                                </td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $order->items->count() }} items</span>
                                </td>
                                <td>
                                    <strong>${{ number_format($order->total, 2) }}</strong>
                                    <div class="small text-muted">
                                        Sub: ${{ number_format($order->subtotal, 2) }}
                                        | Tax: ${{ number_format($order->tax, 2) }}
                                    </div>
                                </td>
                                <td>
    @if($order->payment_status === 'paid' && $order->status === 'pending')
        <span class="badge bg-info">Paid (Pending Fulfillment)</span>
    @elseif($order->status === 'completed')
        <span class="badge bg-success">Completed</span>
    @elseif($order->status === 'processing')
        <span class="badge bg-warning">Processing</span>
    @elseif($order->status === 'cancelled')
        <span class="badge bg-danger">Cancelled</span>
    @else
        <span class="badge bg-secondary">{{ ucfirst($order->status) }}</span>
    @endif
</td>

                            
                                <td>
                                    @if($order->payment_status == 'paid')
                                        <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
                                    @elseif($order->payment_status == 'failed')
                                        <span class="badge bg-danger">{{ ucfirst($order->payment_status) }}</span>
                                    @else
                                        <span class="badge bg-warning">{{ ucfirst($order->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('orders.show', $order) }}" 
                                           class="btn btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('orders.invoice', $order) }}" 
                                           target="_blank" 
                                           class="btn btn-secondary" 
                                           title="View Invoice">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                        <a href="{{ route('orders.edit', $order) }}" 
                                           class="btn btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('orders.destroy', $order) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this order?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    <div class="card-footer">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">
                    Showing {{ $orders->count() }} orders
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Total Orders</h6>
                        <h3 class="mb-0">{{ $orders->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Completed</h6>
                        <h3 class="mb-0">{{ $orders->where('status', 'completed')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Pending</h6>
                        <h3 class="mb-0">{{ $orders->where('status', 'pending')->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="mb-0">Total Revenue</h6>
                        <h3 class="mb-0">${{ number_format($orders->sum('total'), 2) }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Check if DataTable is already initialized
        if (!$.fn.DataTable.isDataTable('#ordersTable')) {
            // Initialize DataTable only if not already initialized
            $('#ordersTable').DataTable({
                pageLength: 10,
                ordering: true,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search orders..."
                }
            });
        }
    });
</script>
@endsection