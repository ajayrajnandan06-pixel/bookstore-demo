<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'invoice_date',
        'due_date',
        'amount',
        'status',
        'notes',
        'pdf_path'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'due_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationship with order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Generate invoice number
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = date('Ymd');
        $lastInvoice = self::latest()->first();
        $number = $lastInvoice ? str_pad((int)substr($lastInvoice->invoice_number, -4) + 1, 4, '0', STR_PAD_LEFT) : '0001';
        
        return $prefix . $date . $number;
    }
}