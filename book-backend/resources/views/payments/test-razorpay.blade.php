@extends('layouts.app')

@section('title', 'Test Razorpay Payment')
@section('page-title', 'Test Razorpay Payment')
@section('page-subtitle', 'Order #' . $order->order_number)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-credit-card me-2"></i>Test Razorpay Payment
                </h5>
            </div>
            <div class="card-body">
                <!-- Order Summary -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Order Details</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td>Order #:</td>
                                        <td class="text-end">{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer:</td>
                                        <td class="text-end">{{ $order->customer->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Amount:</td>
                                        <td class="text-end">${{ number_format($order->total, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Amount Paid:</td>
                                        <td class="text-end">${{ number_format($order->amount_paid, 2) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Due Amount:</td>
                                        <td class="text-end text-danger fw-bold">
                                            ${{ number_format($order->due_amount, 2) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6>Test Cards</h6>
                                <div class="list-group" id="testCardsList">
                                    <!-- Test cards will be loaded here -->
                                </div>
                                <button class="btn btn-sm btn-outline-primary w-100 mt-2" id="loadTestCards">
                                    <i class="fas fa-sync-alt me-1"></i>Load Test Cards
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Amount -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <label class="form-label fw-bold">Payment Amount</label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">$</span>
                            <input type="number" class="form-control" id="amount" 
                                   value="{{ $order->due_amount }}" 
                                   min="1" max="{{ $order->due_amount }}" step="0.01">
                            <button class="btn btn-primary" type="button" id="payButton">
                                <i class="fas fa-lock me-2"></i>Pay Now
                            </button>
                        </div>
                        <div class="form-text">
                            Amount will be converted to INR (₹) for Razorpay
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Quick Amount</label>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="10">
                                $10
                            </button>
                            <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="50">
                                $50
                            </button>
                            <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="100">
                                $100
                            </button>
                            <button class="btn btn-sm btn-outline-secondary quick-amount" data-amount="{{ $order->due_amount }}">
                                Full
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Status Display -->
                <div class="alert alert-info d-none" id="statusAlert">
                    <i class="fas fa-spinner fa-spin me-2"></i>
                    <span id="statusMessage">Processing...</span>
                </div>

                <!-- Payment Result -->
                <div class="card d-none" id="resultCard">
                    <div class="card-body">
                        <h6 id="resultTitle"></h6>
                        <p id="resultMessage"></p>
                        <pre id="resultDetails" class="d-none"></pre>
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-primary" id="backToOrder">
                            <i class="fas fa-arrow-left me-2"></i>Back to Order
                        </a>
                    </div>
                </div>

                <!-- Debug Information -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-bug me-2"></i>Debug Information
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Order Information</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td>Order ID:</td>
                                        <td>{{ $order->id }}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Number:</td>
                                        <td>{{ $order->order_number }}</td>
                                    </tr>
                                    <tr>
                                        <td>Customer ID:</td>
                                        <td>{{ $order->customer_id }}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-6">
                                <h6>Razorpay Configuration</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td>Key ID:</td>
                                        <td>{{ substr(config('services.razorpay.key'), 0, 15) }}...</td>
                                    </tr>
                                    <tr>
                                        <td>Environment:</td>
                                        <td>
                                            <span class="badge bg-warning">Test Mode</span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test Cards Modal -->
<div class="modal fade" id="testCardsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Razorpay Test Cards</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Card Number</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Expiry</th>
                                <th>CVV</th>
                            </tr>
                        </thead>
                        <tbody id="testCardsTable">
                            <!-- Test cards will be loaded here -->
                        </tbody>
                    </table>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Use these test cards to simulate payments. No real money will be charged.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Razorpay Checkout -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    $(document).ready(function() {
        const orderId = {{ $order->id }};
        const dueAmount = {{ $order->due_amount }};
        let currentPayment = null;

        // Load test cards
        $('#loadTestCards').click(function() {
            loadTestCards();
        });

        // Quick amount buttons
        $('.quick-amount').click(function() {
            const amount = $(this).data('amount');
            $('#amount').val(amount);
        });

        // Pay button click
        $('#payButton').click(function() {
            processPayment();
        });

        // Load test cards function
        function loadTestCards() {
            $.ajax({
                url: '/razorpay/test-cards',
                method: 'GET',
                success: function(response) {
                    populateTestCards(response);
                },
                error: function(xhr) {
                    alert('Failed to load test cards');
                }
            });
        }

        // Populate test cards
        function populateTestCards(cards) {
            let html = '';
            cards.forEach(card => {
                html += `
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${card.number}</strong>
                                <div class="small text-muted">${card.name}</div>
                            </div>
                            <span class="badge bg-info">Test Card</span>
                        </div>
                        <small class="text-muted">${card.description}</small>
                        <div class="small mt-1">
                            Expiry: ${card.expiry} | CVV: ${card.cvv}
                        </div>
                    </div>
                `;
            });
            $('#testCardsList').html(html);
        }

        // Process payment
        async function processPayment() {
            const amount = parseFloat($('#amount').val());
            
            if (!amount || amount <= 0) {
                alert('Please enter a valid amount');
                return;
            }

            if (amount > dueAmount) {
                alert('Amount cannot exceed due amount of $' + dueAmount);
                return;
            }

            showStatus('Creating Razorpay order...');

            try {
                // Create Razorpay order
                const response = await $.ajax({
                    url: `/razorpay/test/${orderId}/create-order`,
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        amount: amount
                    }
                });

                if (!response.success) {
                    throw new Error(response.message);
                }

                currentPayment = response.payment_id;
                
                // Initialize Razorpay checkout
                const options = {
                    key: response.key,
                    amount: response.amount,
                    currency: response.currency,
                    name: 'Bookstore Payment',
                    description: 'Payment for Order #{{ $order->order_number }}',
                    order_id: response.order_id,
                    handler: async function(razorpayResponse) {
                        await verifyPayment(razorpayResponse);
                    },
                    prefill: {
                        name: response.customer_name || '',
                        email: response.customer_email || '',
                        contact: response.customer_phone || ''
                    },
                    theme: {
                        color: '#0d6efd'
                    },
                    modal: {
                        ondismiss: function() {
                            showStatus('Payment cancelled by user', 'warning');
                        }
                    }
                };

                const rzp = new Razorpay(options);
                rzp.open();

                showStatus('Redirecting to Razorpay...', 'info');

            } catch (error) {
                showError('Failed to create payment: ' + error.message);
            }
        }

        // Verify payment
        async function verifyPayment(razorpayResponse) {
            showStatus('Verifying payment...', 'info');

            try {
                const verifyResponse = await $.ajax({
                    url: '/payments/razorpay/verify',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        razorpay_order_id: razorpayResponse.razorpay_order_id,
                        razorpay_payment_id: razorpayResponse.razorpay_payment_id,
                        razorpay_signature: razorpayResponse.razorpay_signature,
                        payment_id: currentPayment
                    }
                });

                if (verifyResponse.success) {
                    showSuccess('Payment successful!', verifyResponse.message);
                    setTimeout(() => {
                        window.location.href = verifyResponse.redirect_url;
                    }, 2000);
                } else {
                    showError('Payment verification failed: ' + verifyResponse.message);
                }

            } catch (error) {
                showError('Verification error: ' + error.message);
            }
        }

        // Show status
        function showStatus(message, type = 'info') {
            $('#statusAlert').removeClass('d-none alert-danger alert-success alert-warning')
                            .addClass('alert-' + type);
            $('#statusMessage').html('<i class="fas fa-spinner fa-spin me-2"></i>' + message);
        }

        // Show success
        function showSuccess(title, message) {
            $('#statusAlert').addClass('d-none');
            $('#resultCard').removeClass('d-none');
            $('#resultTitle').html('<i class="fas fa-check-circle text-success me-2"></i>' + title);
            $('#resultMessage').text(message);
            $('#resultCard').removeClass('border-danger').addClass('border-success');
        }

        // Show error
        function showError(message) {
            $('#statusAlert').addClass('d-none');
            $('#resultCard').removeClass('d-none');
            $('#resultTitle').html('<i class="fas fa-times-circle text-danger me-2"></i>Payment Failed');
            $('#resultMessage').text(message);
            $('#resultCard').removeClass('border-success').addClass('border-danger');
        }

        // Load test cards on page load
        loadTestCards();
    });
</script>
@endsection