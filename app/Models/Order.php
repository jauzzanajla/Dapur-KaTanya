<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'pickup_date',
        'pickup_time',
        'pickup_location',
        'notes',
        'payment_method',
        'payment_proof',
        'total_omset',
        'total_modal',
        'net_profit',
        'status',
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'total_omset' => 'float',
        'total_modal' => 'float',
        'net_profit' => 'float',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getPaymentProofUrlAttribute(): ?string
    {
        if (!$this->payment_proof) {
            return null;
        }
        if (str_starts_with($this->payment_proof, 'http')) {
            return $this->payment_proof;
        }
        return \App\Support\Media::url($this->payment_proof);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'menunggu_verifikasi' => [
                'label' => 'Menunggu Verifikasi',
                'bg' => 'bg-amber-100 text-amber-800 border-amber-200',
                'dot' => 'bg-amber-500'
            ],
            'diproses' => [
                'label' => 'Sedang Diproses',
                'bg' => 'bg-blue-100 text-blue-800 border-blue-200',
                'dot' => 'bg-blue-500'
            ],
            'siap_diambil' => [
                'label' => 'Siap Diambil (Pickup)',
                'bg' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'dot' => 'bg-emerald-500'
            ],
            'selesai' => [
                'label' => 'Pesanan Selesai',
                'bg' => 'bg-gray-100 text-gray-800 border-gray-200',
                'dot' => 'bg-gray-500'
            ],
            'dibatalkan' => [
                'label' => 'Dibatalkan',
                'bg' => 'bg-red-100 text-red-800 border-red-200',
                'dot' => 'bg-red-500'
            ],
            default => [
                'label' => ucfirst($this->status),
                'bg' => 'bg-gray-100 text-gray-800 border-gray-200',
                'dot' => 'bg-gray-400'
            ],
        };
    }
}
