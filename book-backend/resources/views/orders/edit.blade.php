@extends('layouts.app')

@section('title', 'Edit Order #' . $order->order_number)
@section('page-title', 'Edit Order #' . $order->order_number)
@section('page-subtitle', 'Update order information')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Edit Order</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.update', $order) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Order Status -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Order Status *</label>
                            <select class="form-control @error('status') is-invalid @enderror" 
                                    id="status" name="status" required>
                                <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Status -->
                        <div class="col-md-6 mb-3">
                            <label for="payment_status" class="form-label">Payment Status *</label>
                            <select class="form-control @error('payment_status') is-invalid @enderror" 
                                    id="payment_status" name="payment_status" required>
                                <option value="pending" {{ $order->payment_status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="paid" {{ $order->payment_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                <option value="failed" {{ $order->payment_status == 'failed' ? 'selected' : '' }}>Failed</option>
                            </select>
                            @error('payment_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-control" id="payment_method" name="payment_method">
                                <option value="">Select payment method</option>
                                <option value="cash" {{ $order->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                                <option value="card" {{ $order->payment_method == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                <option value="online" {{ $order->payment_method == 'online' ? 'selected' : '' }}>Online Payment</option>
                                <option value="bank" {{ $order->payment_method == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Order Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $order->notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Order Items (Read-only) -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-shopping-cart me-2"></i>Order Items</h6>
                        </div>
                        <div class="card-body">
                            @if($order->items->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Book</th>
                                                <th>Price</th>
                                                <th>Qty</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($order->items as $item)
                                                <tr>
                                                    <td>
                                                        @if($item->book)
                                                            {{ $item->book->title }} ({{ $item->book->author }})
                                                        @else
                                                            <span class="text-muted">Book not found</span>
                                                        @endif
                                                    </td>
                                                    <td>${{ number_format($item->price, 2) }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>${{ number_format($item->total, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="mt-3 text-end">
                                    <strong>Order Total: ${{ number_format($order->total, 2) }}</strong>
                                </div>
                            @else
                                <p class="text-muted mb-0">No items in this order.</p>
                            @endif
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Order
                        </button>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <a href="{{ route('orders.index') }}" class="btn btn-info">
                            <i class="fas fa-list"></i> All Orders
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection