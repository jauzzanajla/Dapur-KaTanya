<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RawMaterialPurchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_date',
        'material_name',
        'quantity',
        'unit',
        'total_cost',
        'notes',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'quantity' => 'float',
        'total_cost' => 'float',
    ];
}
