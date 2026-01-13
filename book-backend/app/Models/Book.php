<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'isbn',
        'price',
        'quantity',
        'description',
        'category',
        'publisher',
        'pages',
        'cover_image'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relationship with order items
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Check if book is in stock
    public function isInStock()
    {
        return $this->quantity > 0;
    }

    // Get low stock books
    public function scopeLowStock($query, $threshold = 10)
    {
        return $query->where('quantity', '<', $threshold);
    }
}