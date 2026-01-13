@extends('layouts.app')

@section('title', 'Payment Successful')
@section('page-title', 'Payment Successful')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-success">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-check-circle me-2"></i>Payment Successful
                </h5>
            </div>
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-check-circle fa-5x text-success"></i>
                </div>
                <h3 class="mb-3">Thank You!</h3>
                <p class="text-muted mb-4">
                    Your payment has been processed successfully.
                </p>
                <div class="alert alert-success">
                    <h6>Payment Details</h6>
                    <p class="mb-1">Payment ID: {{ $paymentId ?? 'N/A' }}</p>
                    <p class="mb-0">Order ID: {{ $orderId ?? 'N/A' }}</p>
                </div>
                <div class="mt-4">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary me-2">
                        <i class="fas fa-list me-2"></i>View All Orders
                    </a>
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection