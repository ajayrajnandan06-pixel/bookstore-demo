<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $order->order_number }}</title>
    <style>
        /* Invoice Styles */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        /* Header */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #eaeaea;
        }
        
        .company-info h1 {
            color: #2c3e50;
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .company-info p {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 3px;
        }
        
        .invoice-title h2 {
            color: #3498db;
            font-size: 24px;
            font-weight: 600;
        }
        
        .invoice-title .invoice-number {
            font-size: 18px;
            color: #7f8c8d;
            margin-top: 5px;
        }
        
        /* Invoice Details */
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        
        .detail-box h3 {
            color: #2c3e50;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
            display: inline-block;
        }
        
        .detail-item {
            margin-bottom: 12px;
        }
        
        .detail-item label {
            font-weight: 500;
            color: #7f8c8d;
            display: block;
            font-size: 14px;
        }
        
        .detail-item p {
            font-weight: 500;
            color: #2c3e50;
        }
        
        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
            background: #f8f9fa;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .items-table thead {
            background: #3498db;
            color: white;
        }
        
        .items-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eaeaea;
        }
        
        .items-table tbody tr:hover {
            background: #e3f2fd;
        }
        
        .book-title {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .book-details {
            font-size: 13px;
            color: #7f8c8d;
        }
        
        /* Summary */
        .summary {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
            margin-top: 30px;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #dee2e6;
        }
        
        .summary-row.total {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            border-bottom: none;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #3498db;
        }
        
        /* Status Badges */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-completed {
            background: #d4edda;
            color: #155724;
        }
        
        .status-processing {
            background: #fff3cd;
            color: #856404;
        }
        
        .status-pending {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-unpaid {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Footer */
        .invoice-footer {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eaeaea;
            text-align: center;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .invoice-footer p {
            margin-bottom: 5px;
        }
        
        /* Print Styles */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .invoice-container {
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-btn {
                display: none !important;
            }
        }
        
        /* Print Button */
        .print-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 30px auto;
            transition: all 0.3s;
        }
        
        .print-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="invoice-header">
            <div class="company-info">
                <h1>{{ config('app.name', 'Bookstore') }}</h1>
                <p>123 Book Street, Reading City</p>
                <p>Phone: (123) 456-7890</p>
                <p>Email: info@bookstore.com</p>
                <p>Website: www.bookstore.com</p>
            </div>
            <div class="invoice-title">
                <h2>INVOICE</h2>
                <div class="invoice-number">#{{ $order->order_number }}</div>
                <div class="status-badge status-{{ $order->status }} mt-2">
                    {{ ucfirst($order->status) }}
                </div>
                @if($order->payment_status)
                <div class="status-badge status-{{ $order->payment_status }} mt-1">
                    {{ ucfirst($order->payment_status) }}
                </div>
                @endif
            </div>
        </div>
        <!--Invoice new Tab-->
        <h1>{{ env('INVOICE_COMPANY_NAME', config('app.name')) }}</h1>
<p>{{ env('INVOICE_COMPANY_ADDRESS', 'Your Company Address') }}</p>
        <!-- Invoice Details -->
        <div class="invoice-details">
            <div class="detail-box">
                <h3>BILL TO</h3>
                @if($order->customer)
                <div class="detail-item">
                    <label>Customer Name</label>
                    <p>{{ $order->customer->name }}</p>
                </div>
                @if($order->customer->email)
                <div class="detail-item">
                    <label>Email</label>
                    <p>{{ $order->customer->email }}</p>
                </div>
                @endif
                @if($order->customer->phone)
                <div class="detail-item">
                    <label>Phone</label>
                    <p>{{ $order->customer->phone }}</p>
                </div>
                @endif
                @if($order->customer->address)
                <div class="detail-item">
                    <label>Address</label>
                    <p>{{ $order->customer->address }}</p>
                    @if($order->customer->city)
                    <p>{{ $order->customer->city }}, {{ $order->customer->state }} {{ $order->customer->postal_code }}</p>
                    @endif
                </div>
                @endif
                @else
                <p class="text-muted">Customer information not available</p>
                @endif
            </div>
            
            <div class="detail-box">
                <h3>INVOICE DETAILS</h3>
                <div class="detail-item">
                    <label>Invoice Number</label>
                    <p>#{{ $order->order_number }}</p>
                </div>
                <div class="detail-item">
                    <label>Invoice Date</label>
                    <p>{{ $order->created_at->format('F d, Y') }}</p>
                </div>
                <div class="detail-item">
                    <label>Due Date</label>
                    <p>{{ $order->created_at->addDays(7)->format('F d, Y') }}</p>
                </div>
                <div class="detail-item">
                    <label>Payment Method</label>
                    <p>{{ ucfirst($order->payment_method ?? 'Cash') }}</p>
                </div>
                @if($order->notes)
                <div class="detail-item">
                    <label>Notes</label>
                    <p>{{ $order->notes }}</p>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Items Table -->
        <h3>ORDER ITEMS</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">Item Description</th>
                    <th width="15%">Unit Price</th>
                    <th width="10%">Qty</th>
                    <th width="15%">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <div class="book-title">
                            @if($item->book)
                                {{ $item->book->title }}
                            @else
                                Book not found
                            @endif
                        </div>
                        <div class="book-details">
                            @if($item->book)
                                by {{ $item->book->author }} | ISBN: {{ $item->book->isbn }}
                            @endif
                        </div>
                    </td>
                    <td>${{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Summary -->
        <div class="summary">
            <div class="summary-row">
                <span>Subtotal:</span>
                <span>${{ number_format($order->subtotal, 2) }}</span>
            </div>
            <div class="summary-row">
                <span>Tax (10%):</span>
                <span>${{ number_format($order->tax, 2) }}</span>
            </div>
            @if($order->discount > 0)
            <div class="summary-row">
                <span>Discount:</span>
                <span>-${{ number_format($order->discount, 2) }}</span>
            </div>
            @endif
            <div class="summary-row total">
                <span>TOTAL AMOUNT:</span>
                <span>${{ number_format($order->total, 2) }}</span>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="invoice-footer">
            <p>Thank you for your business!</p>
            <p>For any inquiries, please contact us at info@bookstore.com or call (123) 456-7890</p>
            <p>Invoice generated on {{ now()->format('F d, Y h:i A') }}</p>
        </div>
    </div>
    
    <!-- Print Button -->
    <div class="no-print" style="text-align: center;">
        <button class="print-btn" onclick="window.print()">
            <i class="fas fa-print"></i> Print Invoice
        </button>
        <button class="print-btn" onclick="window.close()" style="background: #6c757d;">
            <i class="fas fa-times"></i> Close Window
        </button>
    </div>
    
    <script>
        // Auto-print option (optional)
        @if(request()->has('autoprint'))
        window.onload = function() {
            window.print();
        }
        @endif
        
        // Add print event listener
        window.addEventListener('afterprint', function() {
            // Optionally close window after printing
            @if(request()->has('autoclose'))
            window.close();
            @endif
        });
    </script>
</body>
</html>