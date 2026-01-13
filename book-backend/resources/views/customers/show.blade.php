@extends('layouts.app')

@section('title', $customer->name)
@section('page-title', $customer->name)
@section('page-subtitle', 'Customer Details')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Customer Details</h5>
                <div>
                    <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('customers.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Customer Info -->
                    <div class="col-md-4">
                        <div class="card mb-4">
                            <div class="card-body text-center">
                                <div class="mb-3">
                                    <div style="width: 100px; height: 100px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                        <i class="fas fa-user fa-3x text-white"></i>
                                    </div>
                                </div>
                                <h4>{{ $customer->name }}</h4>
                                <p class="text-muted">Customer Since: {{ $customer->created_at->format('M d, Y') }}</p>
                                
                                <div class="mt-3">
                                    @if($customer->email)
                                        <p><i class="fas fa-envelope me-2"></i> {{ $customer->email }}</p>
                                    @endif
                                    @if($customer->phone)
                                        <p><i class="fas fa-phone me-2"></i> {{ $customer->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Stats -->
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Customer Statistics</h6>
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Orders
                                        <span class="badge bg-primary rounded-pill">
                                            {{ isset($orders) ? $orders->count() : 0 }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Total Spent
                                        <span class="badge bg-success rounded-pill">
                                            ${{ number_format($customer->orders()->sum('total') ?? 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Last Order
                                        <span class="text-muted">
                                            @if(isset($orders) && $orders->count() > 0)
                                                {{ $orders->first()->created_at->format('M d, Y') }}
                                            @else
                                                Never
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address & Details -->
                    <div class="col-md-8">
                        <!-- Address Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-home me-2"></i>Address Information</h6>
                            </div>
                            <div class="card-body">
                                @if($customer->address)
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <label class="text-muted small">Street Address</label>
                                            <p class="mb-0">{{ $customer->address }}</p>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="text-muted small">City</label>
                                            <p class="mb-0">{{ $customer->city ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-3 mb-2">
                                            <label class="text-muted small">State</label>
                                            <p class="mb-0">{{ $customer->state ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="text-muted small">Postal Code</label>
                                            <p class="mb-0">{{ $customer->postal_code ?? 'N/A' }}</p>
                                        </div>
                                        <div class="col-md-6 mb-2">
                                            <label class="text-muted small">Country</label>
                                            <p class="mb-0">{{ $customer->country ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">No address information available.</p>
                                @endif
                            </div>
                        </div>

                        <!-- Recent Orders - SAFE VERSION -->
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Recent Orders</h6>
                                @if(isset($orders) && $orders->count() > 0)
                                    <a href="#" class="btn btn-sm btn-outline-primary">View All Orders</a>
                                @endif
                            </div>
                            <div class="card-body">
                                @if(isset($orders) && $orders->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Order #</th>
                                                    <th>Date</th>
                                                    <th>Items</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($orders as $order)
                                                    <tr>
                                                        <td><code>{{ $order->order_number ?? 'N/A' }}</code></td>
                                                        <td>{{ $order->created_at->format('M d, Y') ?? 'N/A' }}</td>
                                                        <td>{{ $order->items->count() ?? 0 }} items</td>
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
                                                            <a href="#" class="btn btn-sm btn-info">View</a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <i class="fas fa-shopping-cart fa-2x text-muted mb-3"></i>
                                        <p class="text-muted">No orders found for this customer.</p>
                                        <a href="#" class="btn btn-primary disabled">
                                            <i class="fas fa-plus"></i> Create New Order (Coming Soon)
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4 border-top pt-3">
                    <div class="btn-group" role="group">
                        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit Customer
                        </a>
                        <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" 
                                    onclick="return confirm('Are you sure you want to delete this customer?')">
                                <i class="fas fa-trash"></i> Delete Customer
                            </button>
                        </form>
                        <a href="{{ route('customers.index') }}" class="btn btn-secondary">
                            <i class="fas fa-list"></i> All Customers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection