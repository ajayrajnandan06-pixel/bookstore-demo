@extends('layouts.app')

@section('title', 'Payment Failed')
@section('page-title', 'Payment Failed')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-times-circle me-2"></i>Payment Failed
                </h5>
            </div>
            <div class="card-body text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-times-circle fa-5x text-danger"></i>
                </div>
                <h3 class="mb-3">Payment Failed</h3>
                <p class="text-muted mb-4">
                    We couldn't process your payment. Please try again.
                </p>
                @if(isset($error))
                <div class="alert alert-danger">
                    <h6>Error Details</h6>
                    <p class="mb-0">{{ $error }}</p>
                </div>
                @endif
                <div class="mt-4">
                    <button onclick="window.history.back()" class="btn btn-primary me-2">
                        <i class="fas fa-arrow-left me-2"></i>Try Again
                    </button>
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-danger">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection