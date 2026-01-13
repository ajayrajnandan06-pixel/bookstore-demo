@extends('layouts.app')

@section('title', 'Process Refund')
@section('page-title', 'Process Refund')
@section('page-subtitle', 'Refund for Payment #' . $payment->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Refund Details</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6>Payment Information</h6>
                    <p class="mb-1">Original Amount: ${{ number_format($payment->amount, 2) }}</p>
                    <p class="mb-1">Payment Method: {{ $payment->method_label }}</p>
                    <p class="mb-1">Gateway: {{ $payment->gateway ? ucfirst($payment->gateway) : 'N/A' }}</p>
                    <p class="mb-1">Date: {{ $payment->created_at->format('M d, Y h:i A') }}</p>
                </div>

                <form action="{{ route('payments.refund', $payment) }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Refund Amount *</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0.01" max="{{ $payment->amount }}" 
                                   value="{{ $payment->amount }}" required>
                        </div>
                        <div class="form-text">Maximum refundable amount: ${{ number_format($payment->amount, 2) }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="reason" class="form-label">Refund Reason *</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" 
                                  placeholder="Enter reason for refund..." required></textarea>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone. Please verify the refund details before proceeding.
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-undo me-2"></i>Process Refund
                        </button>
                        <a href="{{ route('orders.show', $payment->order) }}" class="btn btn-secondary">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection