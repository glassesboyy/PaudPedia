<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail_url',
        'file_url',
        'file_type',
        'file_size',
        'price',
        'original_price',
        'is_active',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'item', 'item_type', 'item_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper Methods
    public function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->hasDiscount()) {
            return null;
        }

        return (int) round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (!$this->file_size) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size > 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getTotalPurchasesAttribute(): int
    {
        return $this->orderItems()->whereHas('order', function($query) {
            $query->where('status', 'paid');
        })->sum('quantity');
    }
}
