<?php

namespace App\Models;

use App\Support\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'category',
        'description',
        'badge',
        'modal',
        'harga_jual',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'modal' => 'float',
        'harga_jual' => 'float',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }

    public function primaryImage()
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true);
    }

    public function getPrimaryImageUrlAttribute(): string
    {
        $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
        if ($primary && $primary->image_path) {
            if (str_starts_with($primary->image_path, 'http')) {
                return $primary->image_path;
            }

            try {
                return Media::url($primary->image_path) ?? asset('images/default-food.svg');
            } catch (\Throwable $exception) {
                Log::warning('Primary product image URL could not be generated.', [
                    'product_id' => $this->id,
                    'exception' => $exception->getMessage(),
                ]);

                return asset('images/default-food.svg');
            }
        }

        return asset('images/default-food.svg');
    }

    public function getMarginAttribute(): float
    {
        return (float) ($this->harga_jual - $this->modal);
    }

    public function getMarginPercentageAttribute(): float
    {
        if ($this->harga_jual > 0) {
            return round((($this->harga_jual - $this->modal) / $this->harga_jual) * 100, 1);
        }

        return 0.0;
    }
}
