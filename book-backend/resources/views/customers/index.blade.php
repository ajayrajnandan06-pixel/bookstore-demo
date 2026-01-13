@extends('layouts.app')

@section('title', 'Manage Customers')
@section('page-title', 'Customer Management')
@section('page-subtitle', 'Manage your customer database')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Customers List</h5>
        <a href="{{ route('customers.create') }}" class="btn btn-primary">
            <i class="fas fa-user-plus"></i> Add New Customer
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

        @if($customers->isEmpty())
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h4>No customers found</h4>
                <p class="text-muted">Start by adding your first customer.</p>
                <a href="{{ route('customers.create') }}" class="btn btn-primary">
                    <i class="fas fa-user-plus"></i> Add First Customer
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover" id="customersTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Contact Info</th>
                            <th>Address</th>
                            <th>Orders</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td>
                                    <strong>{{ $customer->name }}</strong>
                                </td>
                                <td>
                                    @if($customer->email)
                                        <div><i class="fas fa-envelope text-muted me-1"></i> {{ $customer->email }}</div>
                                    @endif
                                    @if($customer->phone)
                                        <div><i class="fas fa-phone text-muted me-1"></i> {{ $customer->phone }}</div>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->address)
                                        <div class="small">{{ $customer->address }}</div>
                                        <div class="small text-muted">
                                            {{ $customer->city }}, {{ $customer->state }} {{ $customer->postal_code }}
                                        </div>
                                        @if($customer->country)
                                            <div class="small text-muted">{{ $customer->country }}</div>
                                        @endif
                                    @else
                                        <span class="text-muted">No address</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $customer->orders()->count() }} orders</span>
                                </td>
                                <td>
                                    {{ $customer->created_at->format('M d, Y') }}
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('customers.show', $customer) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('customers.edit', $customer) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('customers.destroy', $customer) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Are you sure you want to delete this customer?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
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
                    Showing {{ $customers->count() }} customers
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
                        <h6 class="mb-0">Total Customers</h6>
                        <h3 class="mb-0">{{ $customers->count() }}</h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x opacity-50"></i>
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
                        <h6 class="mb-0">Active Customers</h6>
                        <h3 class="mb-0">
                            @php
                                // Count customers who have placed at least one order
                                $activeCustomers = $customers->filter(function($customer) {
                                    return $customer->orders()->count() > 0;
                                })->count();
                                echo $activeCustomers;
                            @endphp
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-user-check fa-2x opacity-50"></i>
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
                        <h6 class="mb-0">Total Orders</h6>
                        <h3 class="mb-0">
                            @php
                                // Safely get order count
                                $orderCount = class_exists('\App\Models\Order') ? \App\Models\Order::count() : 0;
                                echo $orderCount;
                            @endphp
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-cart fa-2x opacity-50"></i>
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
                        <h6 class="mb-0">Avg. Orders/Customer</h6>
                        <h3 class="mb-0">
                            @php
                                $totalCustomers = $customers->count();
                                $totalOrders = class_exists('\App\Models\Order') ? \App\Models\Order::count() : 0;
                                
                                if ($totalCustomers > 0 && $totalOrders > 0) {
                                    echo number_format($totalOrders / $totalCustomers, 1);
                                } else {
                                    echo '0.0';
                                }
                            @endphp
                        </h3>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-chart-line fa-2x opacity-50"></i>
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
        if (!$.fn.DataTable.isDataTable('#customersTable')) {
            // Initialize DataTable only if not already initialized
            $('#customersTable').DataTable({
                pageLength: 10,
                ordering: true,
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search customers..."
                }
            });
        }
    });
</script>
@endsection