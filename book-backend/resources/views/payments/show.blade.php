<!-- Payment Section -->
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Payment Information</h5>
        @if($order->due_amount > 0)
            <a href="{{ route('payments.create', $order) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Add Payment
            </a>
        @endif
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr>
                        <th width="50%">Total Amount:</th>
                        <td>${{ number_format($order->total, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Amount Paid:</th>
                        <td>
                            <span class="badge bg-success">
                                ${{ number_format($order->amount_paid, 2) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Due Amount:</th>
                        <td>
                            <span class="badge bg-{{ $order->due_amount > 0 ? 'danger' : 'success' }}">
                                ${{ number_format($order->due_amount, 2) }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Payment Status:</th>
                        <td>
                            @php
                                $statusClass = [
                                    'paid' => 'bg-success',
                                    'partial' => 'bg-warning',
                                    'pending' => 'bg-danger',
                                    'failed' => 'bg-secondary',
                                    'refunded' => 'bg-info'
                                ][$order->payment_status] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $statusClass }}">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                </table>
                
                @if($order->due_amount > 0)
                    <div class="mt-3">
                        <a href="{{ route('payments.create', $order) }}" class="btn btn-primary">
                            <i class="fas fa-credit-card me-2"></i>Make Payment
                        </a>
                    </div>
                @endif
            </div>
            <div class="col-md-6">
                @if($order->payments->count() > 0)
                    <h6>Payment History</h6>
                    <div class="list-group">
                        @foreach($order->payments as $payment)
                            <div class="list-group-item">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${{ number_format($payment->amount, 2) }}</h6>
                                    <small>{{ $payment->created_at->format('M d, Y') }}</small>
                                </div>
                                <p class="mb-1">
                                    {{ $payment->method_label }}
                                    @if($payment->gateway)
                                        via {{ ucfirst($payment->gateway) }}
                                    @endif
                                    @if($payment->status == 'refunded' || $payment->status == 'partially_refunded')
                                        <span class="badge bg-info ms-2">Refunded</span>
                                    @endif
                                </p>
                                <small class="text-muted">
                                    Status: 
                                    <span class="badge {{ $payment->status_badge_class }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </small>
                                
                                @if($payment->isRefundable())
                                    <button class="btn btn-sm btn-outline-danger float-end" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#refundModal{{ $payment->id }}">
                                        Refund
                                    </button>
                                    
                                    <!-- Refund Modal -->
                                    <div class="modal fade" id="refundModal{{ $payment->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Refund Payment</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Refund for payment of ${{ number_format($payment->amount, 2) }} made on {{ $payment->created_at->format('M d, Y') }}</p>
                                                    
                                                    <form action="{{ route('payments.refund', $payment) }}" method="POST" id="refundForm{{ $payment->id }}">
                                                        @csrf
                                                        <div class="mb-3">
                                                            <label class="form-label">Refund Amount</label>
                                                            <input type="number" class="form-control" name="amount" 
                                                                   step="0.01" min="0.01" max="{{ $payment->amount }}" 
                                                                   value="{{ $payment->amount }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">Reason</label>
                                                            <textarea class="form-control" name="reason" rows="3" required></textarea>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" form="refundForm{{ $payment->id }}" class="btn btn-danger">Process Refund</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($payment->method == 'bank_transfer' && $payment->status == 'pending')
                                    <form action="{{ route('payments.verify.bank', $payment) }}" method="POST" class="d-inline float-end me-2">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            Verify
                                        </button>
                                    </form>
                                @endif
                                
                                @if($payment->notes)
                                    <div class="mt-2">
                                        <small class="text-muted">Note: {{ $payment->notes }}</small>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center">No payments recorded</p>
                @endif
            </div>
        </div>
    </div>
</div>