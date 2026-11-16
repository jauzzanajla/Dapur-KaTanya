<?php

namespace App\Models;

use Database\Factories\PickupScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PickupSchedule extends Model
{
    /** @use HasFactory<PickupScheduleFactory> */
    use HasFactory;

    protected $fillable = [
        'pickup_date',
        'is_active',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'is_active' => 'boolean',
    ];
}
