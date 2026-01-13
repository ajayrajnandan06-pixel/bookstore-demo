@extends('layouts.app')

@section('title', 'Process Payment')
@section('page-title', 'Process Payment')
@section('page-subtitle', 'Complete payment for Order #' . $order->order_number)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment Details</h5>
            </div>
            <div class="card-body">
                <!-- Order Summary -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="mb-3">Order Summary</h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Order Total:</span>
                                    <strong>${{ number_format($order->total, 2) }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Amount Paid:</span>
                                    <span class="text-success">${{ number_format($order->amount_paid, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Due Amount:</span>
                                    <span class="text-danger">${{ number_format($order->due_amount, 2) }}</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <strong>Remaining Balance:</strong>
                                    <strong class="text-primary">${{ number_format($order->due_amount, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="mb-3">Customer Information</h6>
                                <p class="mb-2"><strong>Name:</strong> {{ $order->customer->name }}</p>
                                <p class="mb-2"><strong>Email:</strong> {{ $order->customer->email }}</p>
                                <p class="mb-2"><strong>Phone:</strong> {{ $order->customer->phone }}</p>
                                <p class="mb-0"><strong>Status:</strong> 
                                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'partial' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Form -->
                <form action="{{ route('payments.store', $order) }}" method="POST" id="paymentForm">
                    @csrf
                    
                    <!-- Payment Method Selection -->
                    <div class="mb-4">
                        <label class="form-label fw-bold">Payment Method *</label>
                        <div class="row g-3">
                            @foreach(['cash' => 'Cash', 'card' => 'Card', 'online' => 'Online', 'bank_transfer' => 'Bank Transfer'] as $value => $label)
                            <div class="col-md-3">
                                <div class="form-check card payment-method-card">
                                    <input class="form-check-input" type="radio" name="method" 
                                           value="{{ $value }}" id="{{ $value }}" 
                                           {{ old('method', 'cash') == $value ? 'checked' : '' }}>
                                    <label class="form-check-label card-body text-center" for="{{ $value }}">
                                        @php
                                            $icons = [
                                                'cash' => ['icon' => 'money-bill', 'color' => 'success'],
                                                'card' => ['icon' => 'credit-card', 'color' => 'primary'],
                                                'online' => ['icon' => 'globe', 'color' => 'info'],
                                                'bank_transfer' => ['icon' => 'university', 'color' => 'warning']
                                            ];
                                        @endphp
                                        <i class="fas fa-{{ $icons[$value]['icon'] }} fa-2x text-{{ $icons[$value]['color'] }} mb-2"></i>
                                        <h6 class="mb-1">{{ $label }}</h6>
                                        <small class="text-muted">
                                            @if($value == 'cash')
                                                Physical cash payment
                                            @elseif($value == 'card')
                                                Credit/Debit Card
                                            @elseif($value == 'online')
                                                Online payment gateway
                                            @else
                                                Bank wire transfer
                                            @endif
                                        </small>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Online Payment Options - Only Razorpay -->
                    <div id="onlineOptions" class="mb-4" style="display: {{ old('method') == 'online' ? 'block' : 'none' }};">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fas fa-globe me-2"></i>Online Payment Gateway</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Select Gateway *</label>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-check card gateway-card active">
                                                <input class="form-check-input" type="radio" name="gateway" 
                                                       value="razorpay" id="razorpay" checked style="display: none;">
                                                <label class="form-check-label card-body text-center" for="razorpay">
                                                    <i class="fas fa-rupee-sign fa-2x text-success mb-2"></i>
                                                    <h6 class="mb-1">Razorpay</h6>
                                                    <small class="text-muted">Indian Payments</small>
                                                    <div class="mt-2">
                                                        <small class="text-info">
                                                            <i class="fas fa-check-circle"></i> Only payment gateway available
                                                        </small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @error('gateway')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card Details (for manual card entry) -->
                    <div id="cardDetails" class="mb-4" style="display: {{ old('method') == 'card' ? 'block' : 'none' }};">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0"><i class="fas fa-credit-card me-2"></i>Card Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Card Number</label>
                                        <input type="text" class="form-control" name="card_number" 
                                               placeholder="1234 5678 9012 3456" 
                                               value="{{ old('card_number') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Expiry</label>
                                        <input type="text" class="form-control" name="card_expiry" 
                                               placeholder="MM/YY" value="{{ old('card_expiry') }}">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">CVV</label>
                                        <input type="text" class="form-control" name="card_cvv" 
                                               placeholder="123" value="{{ old('card_cvv') }}">
                                    </div>
                                </div>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    This is for manual card entry (like terminal transactions). For online card payments, select "Online" method.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bank Transfer Details -->
                    <div id="bankDetails" class="mb-4" style="display: {{ old('method') == 'bank_transfer' ? 'block' : 'none' }};">
                        <div class="card">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0"><i class="fas fa-university me-2"></i>Bank Transfer Details</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Reference Number</label>
                                    <input type="text" class="form-control" name="reference_number" 
                                           placeholder="Enter bank reference/UTR number" 
                                           value="{{ old('reference_number') }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" name="bank_name" 
                                           placeholder="Enter bank name" value="{{ old('bank_name') }}">
                                </div>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Payment will be marked as pending until verified by admin.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Amount -->
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <label for="amount" class="form-label fw-bold">Payment Amount *</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('amount') is-invalid @enderror" 
                                       id="amount" name="amount" 
                                       step="0.01" min="0.01" max="{{ $order->due_amount }}" 
                                       value="{{ old('amount', $order->due_amount) }}" required>
                                <button type="button" class="btn btn-outline-secondary" id="btnFullAmount">
                                    Full Amount
                                </button>
                            </div>
                            @error('amount')
                                <div class="text-danger mt-1">{{ $message }}</div>
                            @enderror
                            <div class="form-text mt-2">
                                <span id="maxAmountText">Maximum: ${{ number_format($order->due_amount, 2) }}</span>
                                <span class="ms-3" id="partialPaymentInfo" style="display: none;">
                                    <i class="fas fa-info-circle text-warning"></i>
                                    This will be a partial payment
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Quick Amount</label>
                            <div class="d-flex flex-wrap gap-2">
                                @php
                                    $quickAmounts = [
                                        min($order->due_amount, 10),
                                        min($order->due_amount, 50),
                                        min($order->due_amount, 100),
                                        $order->due_amount / 2
                                    ];
                                @endphp
                                @foreach($quickAmounts as $quickAmount)
                                    @if($quickAmount > 0)
                                    <button type="button" class="btn btn-outline-primary btn-sm quick-amount" 
                                            data-amount="{{ $quickAmount }}">
                                        ${{ number_format($quickAmount, 2) }}
                                    </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Payment Notes -->
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-bold">Payment Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="Enter any payment notes, reference numbers, or additional information...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Order
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-check-circle me-2"></i>Process Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Payment History Sidebar -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Payment History</h5>
            </div>
            <div class="card-body">
                @if($order->payments->count() > 0)
                    <div class="list-group">
                        @foreach($order->payments->sortByDesc('created_at') as $payment)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${{ number_format($payment->amount, 2) }}</h6>
                                    <small>{{ $payment->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small>
                                        <i class="fas fa-{{ $payment->method == 'cash' ? 'money-bill' : ($payment->method == 'card' ? 'credit-card' : ($payment->method == 'online' ? 'globe' : 'university')) }} me-1"></i>
                                        {{ ucfirst($payment->method) }}
                                        @if($payment->gateway)
                                            <span class="badge bg-secondary ms-1">{{ ucfirst($payment->gateway) }}</span>
                                        @endif
                                    </small>
                                    <span class="badge bg-{{ $payment->status == 'completed' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </div>
                                @if($payment->notes)
                                    <small class="text-muted d-block mt-1">{{ Str::limit($payment->notes, 50) }}</small>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">No payments recorded yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .payment-method-card, .gateway-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    
    .payment-method-card:hover, .gateway-card:hover {
        border-color: #dee2e6;
        transform: translateY(-2px);
    }
    
    .payment-method-card.active, .gateway-card.active {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
</style>
@endsection

@section('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    $(document).ready(function() {
        // Payment method selection
        $('input[name="method"]').change(function() {
            const method = $(this).val();
            
            // Hide all sections
            $('#onlineOptions, #cardDetails, #bankDetails').hide();
            
            // Show relevant section
            if (method === 'online') {
                $('#onlineOptions').show();
            } else if (method === 'card') {
                $('#cardDetails').show();
            } else if (method === 'bank_transfer') {
                $('#bankDetails').show();
            }
            
            // Update payment method card styling
            $('.payment-method-card').removeClass('active');
            $(this).closest('.payment-method-card').addClass('active');
        });
        
        // Gateway selection - Only Razorpay, so no need for change handler
        
        // Full amount button
        $('#btnFullAmount').click(function() {
            const maxAmount = parseFloat($('#amount').attr('max'));
            $('#amount').val(maxAmount.toFixed(2)).trigger('input');
        });
        
        // Quick amount buttons
        $('.quick-amount').click(function() {
            const amount = parseFloat($(this).data('amount'));
            $('#amount').val(amount.toFixed(2)).trigger('input');
        });
        
        // Amount input handling
        $('#amount').on('input', function() {
            const amount = parseFloat($(this).val()) || 0;
            const maxAmount = parseFloat($(this).attr('max'));
            
            // Show/hide partial payment info
            if (amount < maxAmount && amount > 0) {
                $('#partialPaymentInfo').show();
            } else {
                $('#partialPaymentInfo').hide();
            }
        });
        
        // Form submission
        $('#paymentForm').submit(async function(e) {
            e.preventDefault();
            
            const method = $('input[name="method"]:checked').val();
            const amount = parseFloat($('#amount').val());
            
            if (!method) {
                alert('Please select a payment method.');
                return false;
            }
            
            if (!amount || amount <= 0) {
                alert('Please enter a valid payment amount.');
                return false;
            }
            
            // Show loading
            $('#submitBtn').html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
            $('#submitBtn').prop('disabled', true);
            
            if (method === 'online') {
                // Only Razorpay is available
                await processOnlinePayment(method, amount);
                return false;
            } else {
                // For cash, card, bank transfer - submit form normally
                this.submit();
            }
        });
        
        // Process online payment (only Razorpay)
        async function processOnlinePayment(method, amount) {
            const orderId = {{ $order->id }};
            const notes = $('#notes').val();
            
            try {
                await processRazorpayPayment(orderId, amount, notes);
            } catch (error) {
                alert('Payment failed: ' + error.message);
                resetSubmitButton();
            }
        }
        
        // Process Razorpay payment
        async function processRazorpayPayment(orderId, amount, notes) {
            try {
                // Create Razorpay order via API
                const response = await fetch(`/api/payments/razorpay/${orderId}/create-order`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        amount: amount,
                        notes: notes
                    })
                });
                
                const data = await response.json();
                
                if (!data.success) {
                    throw new Error(data.message || 'Failed to create Razorpay order');
                }
                
                // Open Razorpay checkout
                const options = {
                    key: data.razorpay_key,
                    amount: data.amount,
                    currency: data.currency,
                    name: 'Bookstore Payment',
                    description: 'Payment for Order #{{ $order->order_number }}',
                    order_id: data.order_id,
                    handler: async function(razorpayResponse) {
                        await verifyRazorpayPayment(razorpayResponse, data.payment_id);
                    },
                    prefill: {
                        name: data.customer_name || '',
                        email: data.customer_email || '',
                        contact: data.customer_phone || ''
                    },
                    theme: {
                        color: '#0d6efd'
                    },
                    modal: {
                        ondismiss: function() {
                            resetSubmitButton();
                            alert('Payment cancelled');
                        }
                    }
                };
                
                const rzp = new Razorpay(options);
                rzp.open();
                
            } catch (error) {
                throw error;
            }
        }
        
        // Verify Razorpay payment
        async function verifyRazorpayPayment(razorpayResponse, paymentId) {
            try {
                const verifyResponse = await fetch('/payments/razorpay/verify', {
                    method: 'POST',
                    headers: {   
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    body: JSON.stringify({
                        razorpay_order_id: razorpayResponse.razorpay_order_id,
                        razorpay_payment_id: razorpayResponse.razorpay_payment_id,
                        razorpay_signature: razorpayResponse.razorpay_signature,
                        payment_id: paymentId
                    })
                });
                
                const data = await verifyResponse.json();
                
                if (data.success) {
                    // Redirect to order page
                    window.location.href = data.redirect_url || '{{ route("orders.show", $order) }}';
                } else {
                    throw new Error(data.message || 'Payment verification failed');
                }
            } catch (error) {
                resetSubmitButton();
                alert('Payment verification failed: ' + error.message);
            }
        }
        
        // Reset submit button
        function resetSubmitButton() {
            $('#submitBtn').html('<i class="fas fa-check-circle me-2"></i>Process Payment');
            $('#submitBtn').prop('disabled', false);
        }
        
        // Initialize
        $('input[name="method"]:checked').trigger('change');
        $('#gateway-card').addClass('active');
        $('#amount').trigger('input');
    });
</script>
@endsection