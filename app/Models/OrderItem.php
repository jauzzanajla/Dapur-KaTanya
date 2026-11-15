<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'quantity',
        'price',
        'modal',
        'subtotal',
        'subtotal_modal',
        'subtotal_profit',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price' => 'float',
        'modal' => 'float',
        'subtotal' => 'float',
        'subtotal_modal' => 'float',
        'subtotal_profit' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
