@extends('layouts.app')

@section('title', 'Stripe Payment')
@section('page-title', 'Stripe Payment')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Complete Your Payment</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <i class="fab fa-stripe fa-4x text-primary mb-3"></i>
                    <h4>Order #{{ $order->order_number }}</h4>
                    <p class="lead">Amount: ${{ number_format($payment->amount, 2) }}</p>
                </div>

                <div id="stripe-card-element" class="form-control p-3"></div>
                <div id="stripe-card-errors" class="text-danger mt-2"></div>

                <div class="d-grid gap-2 mt-4">
                    <button class="btn btn-primary btn-lg" id="stripe-submit-button">
                        <i class="fab fa-stripe me-2"></i>Pay ${{ number_format($payment->amount, 2) }}
                    </button>
                    <a href="{{ route('payments.stripe.cancel', [$order, $payment]) }}" class="btn btn-secondary">
                        Cancel Payment
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card');
    
    cardElement.mount('#stripe-card-element');

    const submitButton = document.getElementById('stripe-submit-button');
    const clientSecret = '{{ $paymentIntent->client_secret }}';

    submitButton.addEventListener('click', async () => {
        submitButton.disabled = true;
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';

        const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement
            }
        });

        if (error) {
            document.getElementById('stripe-card-errors').textContent = error.message;
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="fab fa-stripe me-2"></i>Pay ${{ number_format($payment->amount, 2) }}';
        } else if (paymentIntent.status === 'succeeded') {
            window.location.href = '{{ route("payments.stripe.success", [$order, $payment]) }}';
        }
    });
</script>
@endsection