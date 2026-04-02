<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    /** @use HasFactory<\Database\Factories\OrderItemFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'product_price',
        'subtotal',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Получить заказ, к которому относится позиция
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Получить продукт
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
