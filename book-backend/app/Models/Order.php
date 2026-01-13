<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'subtotal',
        'tax',
        'discount',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'notes'
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationship with customer
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Relationship with order items
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relationship with invoice
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }

    // Relationship with payments
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Generate order number
    public static function generateOrderNumber()
    {
        $prefix = 'ORD';
        $date = date('Ymd');
        $lastOrder = self::latest()->first();
        $number = $lastOrder ? str_pad((int)substr($lastOrder->order_number, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        
        return $prefix . $date . $number;
    }

    /**
     * Accessors
     */
    public function getAmountPaidAttribute()
    {
        return $this->payments()->where('status', 'completed')->sum('amount');
    }

    public function getDueAmountAttribute()
    {
        return max(0, $this->total - $this->amount_paid);
    }

    /**
     * Check if order is fully paid
     */
    public function isFullyPaid()
    {
        return $this->due_amount <= 0;
    }

    /**
     * Check if order has partial payment
     */
    public function hasPartialPayment()
    {
        return $this->amount_paid > 0 && $this->amount_paid < $this->total;
    }

    /**
     * Update payment status AND order status based on payments
     */
    public function updatePaymentStatus()
    {
        $amountPaid = $this->amount_paid;
        
        // Determine payment status
        if ($amountPaid >= $this->total) {
            $paymentStatus = 'paid';
        } elseif ($amountPaid > 0) {
            $paymentStatus = 'partial';
        } else {
            $paymentStatus = 'pending';
        }
        
        // Update the payment status
        $this->payment_status = $paymentStatus;
        
        // IMPORTANT: Also update order status if fully paid and currently pending
        if ($paymentStatus === 'paid' && $this->status === 'pending') {
            $this->status = 'completed';
        }
        
        $this->save();
        
        return $paymentStatus;
    }

    /**
     * Record a payment
     */
    public function recordPayment($data)
    {
        $payment = $this->payments()->create([
            'amount' => $data['amount'],
            'method' => $data['method'],
            'gateway' => $data['gateway'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'gateway_response' => $data['gateway_response'] ?? null,
            'paid_at' => $data['paid_at'] ?? ($data['status'] === 'completed' ? now() : null),
            'notes' => $data['notes'] ?? null,
        ]);

        // Update order payment status AND order status
        $this->updatePaymentStatus();

        return $payment;
    }

    /**
     * Scope for paid orders
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for completed orders
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Mark order as completed
     */
    public function markAsCompleted()
    {
        $this->status = 'completed';
        $this->save();
        
        return $this;
    }

    /**
     * Mark order as paid (manual override)
     */
    public function markAsPaid()
    {
        $this->payment_status = 'paid';
        
        // Also update order status to completed if it's pending
        if ($this->status === 'pending') {
            $this->status = 'completed';
        }
        
        $this->save();
        
        return $this;
    }

    /**
     * Cancel order
     */
    public function cancel()
    {
        $this->status = 'cancelled';
        $this->save();
        
        return $this;
    }
}