@extends('layouts.app')

@section('title', 'Razorpay Payment')
@section('page-title', 'Razorpay Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Complete Your Payment</h5>
            </div>
            <div class="card-body text-center">
                <i class="fas fa-rupee-sign fa-4x text-success mb-3"></i>
                <h4>Order #{{ $order->order_number }}</h4>
                <p class="lead">Amount: ₹{{ number_format($payment->amount * 83, 2) }}</p>
                <p class="text-muted">(${{ number_format($payment->amount, 2) }})</p>

                <button class="btn btn-success btn-lg" id="razorpay-button">
                    <i class="fas fa-rupee-sign me-2"></i>Pay ₹{{ number_format($payment->amount * 83, 2) }}
                </button>
                
                <div class="mt-3">
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                        Cancel Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.getElementById('razorpay-button').onclick = function(e) {
        const options = {
            key: '{{ config("services.razorpay.key") }}',
            amount: '{{ $razorpayOrder->amount }}',
            currency: '{{ $razorpayOrder->currency }}',
            name: '{{ config("app.name") }}',
            description: 'Order #{{ $order->order_number }}',
            order_id: '{{ $razorpayOrder->id }}',
            handler: function(response) {
                // Submit verification form
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '{{ route("payments.razorpay.verify") }}';
                
                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = '{{ csrf_token() }}';
                form.appendChild(tokenInput);
                
                const orderIdInput = document.createElement('input');
                orderIdInput.type = 'hidden';
                orderIdInput.name = 'razorpay_order_id';
                orderIdInput.value = response.razorpay_order_id;
                form.appendChild(orderIdInput);
                
                const paymentIdInput = document.createElement('input');
                paymentIdInput.type = 'hidden';
                paymentIdInput.name = 'razorpay_payment_id';
                paymentIdInput.value = response.razorpay_payment_id;
                form.appendChild(paymentIdInput);
                
                const signatureInput = document.createElement('input');
                signatureInput.type = 'hidden';
                signatureInput.name = 'razorpay_signature';
                signatureInput.value = response.razorpay_signature;
                form.appendChild(signatureInput);
                
                document.body.appendChild(form);
                form.submit();
            },
            prefill: {
                name: '{{ $order->customer->name }}',
                email: '{{ $order->customer->email }}',
                contact: '{{ $order->customer->phone }}'
            },
            theme: {
                color: '#3498db'
            }
        };
        
        const rzp = new Razorpay(options);
        rzp.open();
        e.preventDefault();
    }
</script>
@endsection