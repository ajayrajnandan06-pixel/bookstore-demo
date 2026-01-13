@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)
@section('page-title', 'Order #' . $order->order_number)
@section('page-subtitle', 'Order Details')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">

            {{-- Header --}}
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order Details</h5>
                <div class="btn-group">
                    <a href="{{ route('orders.edit', $order) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('orders.invoice.download', $order) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> PDF
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>

            <div class="card-body">

                {{-- Alerts --}}
                @foreach (['success','error'] as $msg)
                    @if(session($msg))
                        <div class="alert alert-{{ $msg == 'success' ? 'success' : 'danger' }} alert-dismissible fade show">
                            {{ session($msg) }}
                            <button class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                @endforeach

                <div class="row">
                    {{-- LEFT SIDE --}}
                    <div class="col-md-6">

                        {{-- Order Info --}}
                        <div class="card mb-4">
                            <div class="card-header"><strong>Order Info</strong></div>
                            <div class="card-body">
                                <p><b>Order #:</b> {{ $order->order_number }}</p>
                                <p><b>Date:</b> {{ $order->created_at->format('d M Y') }}</p>
                                <p>
                                    <b>Status:</b>
                                    <span class="badge bg-success status-badge">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                                <p>
                                    <b>Payment:</b>
                                    <span class="badge bg-info payment-status-badge">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        {{-- Customer --}}
                        <div class="card mb-4">
                            <div class="card-header"><strong>Customer</strong></div>
                            <div class="card-body">
                                @if($order->customer)
                                    <p><b>Name:</b> {{ $order->customer->name }}</p>
                                    <p><b>Email:</b> {{ $order->customer->email }}</p>
                                    <p><b>Phone:</b> {{ $order->customer->phone }}</p>
                                @else
                                    <p class="text-muted">No customer linked</p>
                                @endif
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT SIDE --}}
                    <div class="col-md-6">

                        {{-- Items --}}
                        <div class="card mb-4">
                            <div class="card-header"><strong>Order Items</strong></div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Book</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->items as $item)
                                        <tr>
                                            <td>{{ $item->book->title ?? 'N/A' }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->total,2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Summary --}}
                        <div class="card">
                            <div class="card-header"><strong>Summary</strong></div>
                            <div class="card-body">
                                <p>Subtotal: ${{ number_format($order->subtotal,2) }}</p>
                                <p>Tax: ${{ number_format($order->tax,2) }}</p>
                                <p>Discount: ${{ number_format($order->discount,2) }}</p>
                                <hr>
                                <h5>Total: ${{ number_format($order->total,2) }}</h5>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- PAYMENT SECTION (Merged) --}}
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between">
                        <strong>Payment Information</strong>
                        @if($order->due_amount > 0)
                            <a href="{{ route('payments.create',$order) }}" class="btn btn-primary btn-sm">
                                Add Payment
                            </a>
                        @endif
                    </div>

                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <th>Total</th>
                                <td>${{ number_format($order->total,2) }}</td>
                            </tr>
                            <tr>
                                <th>Paid</th>
                                <td>${{ number_format($order->amount_paid,2) }}</td>
                            </tr>
                            <tr>
                                <th>Due</th>
                                <td>${{ number_format($order->due_amount,2) }}</td>
                            </tr>
                        </table>

                        <h6>Payment History</h6>
                        @forelse($order->payments as $payment)
                            <div class="border p-2 mb-2">
                                ${{ number_format($payment->amount,2) }} -
                                {{ ucfirst($payment->method) }} -
                                {{ ucfirst($payment->status) }}
                            </div>
                        @empty
                            <p class="text-muted">No payments yet</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
