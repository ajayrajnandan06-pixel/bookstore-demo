@extends('layouts.app')

@section('title', 'Create New Order')
@section('page-title', 'Create New Order')
@section('page-subtitle', 'Create a new order for a customer')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">New Order</h5>
            </div>
            <div class="card-body">
                <form id="orderForm" action="{{ route('orders.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <!-- Customer Selection -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Customer Information</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Existing Customer Selection -->
                                    <!-- <div class="mb-3">
                                        <label for="customer_id" class="form-label">Select Existing Customer (Optional)</label>
                                        <select class="form-control @error('customer_id') is-invalid @enderror" 
                                                id="customer_id" name="customer_id">
                                            <option value="">Create New Customer</option>
                                            @foreach($customers as $customer)
                                                <option value="{{ $customer->id }}" 
                                                    {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->name }} 
                                                    @if($customer->email)
                                                        ({{ $customer->email }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="form-text">Leave blank to create new customer</div>
                                        @error('customer_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div> -->
                                    
                                    <!-- New Customer Fields (Initially hidden) -->
                                    <div id="newCustomerFields" class="border-top pt-3 mt-3">
                                        <h6>New Customer Details</h6>
                                        
                                        <div class="mb-3">
                                            <label for="customer_name" class="form-label">Customer Name *</label>
                                            <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                                   id="customer_name" name="customer_name" 
                                                   value="{{ old('customer_name') }}">
                                            @error('customer_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="customer_email" class="form-label">Email</label>
                                                <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                                                       id="customer_email" name="customer_email" 
                                                       value="{{ old('customer_email') }}">
                                                @error('customer_email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label for="customer_phone" class="form-label">Phone</label>
                                                <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                                                       id="customer_phone" name="customer_phone" 
                                                       value="{{ old('customer_phone') }}">
                                                @error('customer_phone')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="customer_address" class="form-label">Address</label>
                                            <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                                      id="customer_address" name="customer_address" rows="2">{{ old('customer_address') }}</textarea>
                                            @error('customer_address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <!-- Existing Customer Info Display -->
                                    <div id="existingCustomerInfo" class="mt-3 p-3 bg-light rounded" style="display: none;">
                                        <h6>Selected Customer Information</h6>
                                        <p id="customerEmail" class="mb-1"></p>
                                        <p id="customerPhone" class="mb-1"></p>
                                        <p id="customerAddress" class="mb-0"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Details -->
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Order Details</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="payment_method" class="form-label">Payment Method</label>
                                        <select class="form-control" id="payment_method" name="payment_method">
                                            <option value="">Select payment method</option>
                                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
                                            <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>Online Payment</option>
                                            <option value="bank" {{ old('payment_method') == 'bank' ? 'selected' : '' }}>Bank Transfer</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="notes" class="form-label">Order Notes</label>
                                        <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-book me-2"></i>Order Items</h6>
                            <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                                <i class="fas fa-plus"></i> Add Item
                            </button>
                        </div>
                        <div class="card-body">
                            <div id="orderItems">
                                <!-- Items will be added here dynamically -->
                                <div class="text-center py-3" id="noItemsMessage">
                                    <i class="fas fa-book fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">No items added yet. Click "Add Item" to start.</p>
                                </div>
                            </div>
                            
                            <!-- Order Summary -->
                            <div class="row mt-4">
                                <div class="col-md-6 offset-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h6 class="mb-3">Order Summary</h6>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Subtotal:</span>
                                                <span id="subtotal">$0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Tax (10%):</span>
                                                <span id="tax">$0.00</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2">
                                                <span>Discount:</span>
                                                <span id="discount">$0.00</span>
                                            </div>
                                            <hr>
                                            <div class="d-flex justify-content-between">
                                                <strong>Total:</strong>
                                                <strong id="total">$0.00</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Create Order
                        </button>
                        <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Item Template (Hidden) -->
<div id="itemTemplate" style="display: none;">
    <div class="card mb-3 item-row">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Book *</label>
                    <select class="form-control book-select" name="items[INDEX][book_id]" required>
                        <option value="">Select a book</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}" 
                                data-price="{{ $book->price }}"
                                data-stock="{{ $book->quantity }}">
                                {{ $book->title }} ({{ $book->author }}) - ${{ number_format($book->price, 2) }} (Stock: {{ $book->quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantity *</label>
                    <input type="number" class="form-control quantity-input" 
                           name="items[INDEX][quantity]" min="1" value="1" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Price</label>
                    <input type="text" class="form-control price-display" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Total</label>
                    <input type="text" class="form-control total-display" readonly>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-item-btn">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        let itemCount = 0;
        const books = @json($books->keyBy('id'));
        let customers = @json($customers->keyBy('id'));
        
        // Toggle between new and existing customer fields
        $('#customer_id').change(function() {
            const customerId = $(this).val();
            
            if (customerId) {
                // Show existing customer info
                $('#newCustomerFields').hide();
                $('#existingCustomerInfo').show();
                
                // In real app, fetch customer details via AJAX
                if (customers[customerId]) {
                    const customer = customers[customerId];
                    $('#customerEmail').text('Email: ' + (customer.email || 'N/A'));
                    $('#customerPhone').text('Phone: ' + (customer.phone || 'N/A'));
                    $('#customerAddress').text('Address: ' + (customer.address || 'N/A'));
                }
                
                // Disable new customer fields (optional)
                $('#customer_name, #customer_email, #customer_phone, #customer_address')
                    .prop('required', false)
                    .val('');
            } else {
                // Show new customer fields
                $('#newCustomerFields').show();
                $('#existingCustomerInfo').hide();
                
                // Enable new customer fields
                $('#customer_name').prop('required', true);
            }
        });
        
        // Add item button
        $('#addItemBtn').click(function() {
            const template = $('#itemTemplate').html();
            const newItem = template.replace(/INDEX/g, itemCount);
            
            $('#noItemsMessage').hide();
            $('#orderItems').append(newItem);
            
            // Initialize the new item
            const newRow = $('#orderItems .item-row').last();
            initializeItemRow(newRow);
            
            itemCount++;
            updateOrderSummary();
        });
        
        // Initialize an item row
        function initializeItemRow(row) {
            const bookSelect = row.find('.book-select');
            const quantityInput = row.find('.quantity-input');
            const priceDisplay = row.find('.price-display');
            const totalDisplay = row.find('.total-display');
            
            // Book select change
            bookSelect.change(function() {
                const bookId = $(this).val();
                if (bookId && books[bookId]) {
                    const book = books[bookId];
                    const price = parseFloat(book.price);
                    priceDisplay.val('$' + parseFloat(book.price).toFixed(2));
                    
                    // Set max quantity based on stock
                    quantityInput.attr('max', book.stock);
                    
                    // Check if current quantity exceeds stock
                    if (quantityInput.val() > book.stock) {
                        quantityInput.val(book.stock);
                        alert(`Maximum available stock: ${book.stock}`);
                    }
                    
                    updateItemTotal(row);
                } else {
                    priceDisplay.val('');
                    totalDisplay.val('');
                }
            });
            
            // Quantity input change
            quantityInput.on('input', function() {
                const bookId = bookSelect.val();
                if (bookId && books[bookId]) {
                    const book = books[bookId];
                    const quantity = parseInt($(this).val()) || 0;
                    
                    if (quantity > book.stock) {
                        $(this).val(book.stock);
                        alert(`Maximum available stock: ${book.stock}`);
                    }
                    
                    updateItemTotal(row);
                }
            });
            
            // Remove button
            row.find('.remove-item-btn').click(function() {
                row.remove();
                updateOrderSummary();
                
                // Show no items message if all items removed
                if ($('#orderItems .item-row').length === 0) {
                    $('#noItemsMessage').show();
                }
            });
        }
        
        // Update item total
        function updateItemTotal(row) {
            const bookSelect = row.find('.book-select');
            const quantityInput = row.find('.quantity-input');
            const totalDisplay = row.find('.total-display');
            
            const bookId = bookSelect.val();
            const quantity = parseInt(quantityInput.val()) || 0;
            
            if (bookId && books[bookId] && quantity > 0) {
                const book = books[bookId];
                const total = parseFloat(book.price) * quantity;
                totalDisplay.val('$' + total.toFixed(2));
            } else {
                totalDisplay.val('');
            }
            
            updateOrderSummary();
        }
        
        // Update order summary
        function updateOrderSummary() {
            let subtotal = 0;
            
            $('#orderItems .item-row').each(function() {
                const totalDisplay = $(this).find('.total-display').val();
                if (totalDisplay) {
                    const total = parseFloat(totalDisplay.replace('$', '')) || 0;
                    subtotal += total;
                }
            });
            
            const tax = subtotal * 0.10; // 10% tax
            const total = subtotal + tax;
            
            $('#subtotal').text('$' + subtotal.toFixed(2));
            $('#tax').text('$' + tax.toFixed(2));
            $('#total').text('$' + total.toFixed(2));
        }
        
        // Form validation
        $('#orderForm').submit(function(e) {
            // Validate at least one item
            if ($('#orderItems .item-row').length === 0) {
                e.preventDefault();
                alert('Please add at least one item to the order.');
                return false;
            }
            
            // Validate customer information
            const customerId = $('#customer_id').val();
            const customerName = $('#customer_name').val().trim();
            
            if (!customerId && !customerName) {
                e.preventDefault();
                alert('Please select an existing customer or enter a new customer name.');
                $('#customer_name').focus();
                return false;
            }
            
            // Validate each item
            let isValid = true;
            $('#orderItems .item-row').each(function() {
                const bookSelect = $(this).find('.book-select');
                const quantityInput = $(this).find('.quantity-input');
                
                if (!bookSelect.val()) {
                    isValid = false;
                    bookSelect.addClass('is-invalid');
                } else {
                    bookSelect.removeClass('is-invalid');
                }
                
                if (!quantityInput.val() || parseInt(quantityInput.val()) < 1) {
                    isValid = false;
                    quantityInput.addClass('is-invalid');
                } else {
                    quantityInput.removeClass('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields for order items.');
                return false;
            }
        });
        
        // Add first item automatically
        $('#addItemBtn').click();
        
        // Trigger initial state
        $('#customer_id').trigger('change');
    });
</script>
@endsection