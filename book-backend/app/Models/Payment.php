<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'gateway',
        'transaction_id',
        'gateway_transaction_id',
        'status',
        'gateway_response',
        'paid_at',
        'notes'
    ];

    protected $casts = [
        'gateway_response' => 'array',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Relationships
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope methods
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful()
    {
        return in_array($this->status, ['completed', 'refunded', 'partially_refunded']);
    }

    /**
     * Check if payment is refundable
     */
    public function isRefundable()
    {
        return $this->status === 'completed' && $this->amount > 0;
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute()
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Get payment method label
     */
    public function getMethodLabelAttribute()
    {
        $labels = [
            'cash' => 'Cash',
            'card' => 'Credit/Debit Card',
            'online' => 'Online Payment',
            'bank_transfer' => 'Bank Transfer'
        ];

        return $labels[$this->method] ?? ucfirst($this->method);
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'bg-warning',
            'completed' => 'bg-success',
            'failed' => 'bg-danger',
            'refunded' => 'bg-info',
            'partially_refunded' => 'bg-info'
        ];

        return $classes[$this->status] ?? 'bg-secondary';
    }
}