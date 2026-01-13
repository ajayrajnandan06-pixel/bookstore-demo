<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'book_id',
        'quantity',
        'price',
        'total'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    // Relationship with order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Relationship with book
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    // Calculate total automatically
    public static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->total = $item->price * $item->quantity;
        });

        static::updating(function ($item) {
            $item->total = $item->price * $item->quantity;
        });
    }
}