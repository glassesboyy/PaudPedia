<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'thumbnail_url',
        'file_url',
        'price',
        'original_price',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        // Clean up old files when thumbnail_url or file_url changes or product is deleted
        static::updating(function (Product $product) {
            // Clean up old thumbnail (public disk)
            if ($product->isDirty('thumbnail_url')) {
                $oldThumbnail = $product->getOriginal('thumbnail_url');
                if ($oldThumbnail && Storage::disk('public')->exists($oldThumbnail)) {
                    Storage::disk('public')->delete($oldThumbnail);
                }
            }

            // Clean up old file (local/private disk for downloadable content)
            if ($product->isDirty('file_url')) {
                $oldFile = $product->getOriginal('file_url');
                if ($oldFile && Storage::disk('local')->exists($oldFile)) {
                    Storage::disk('local')->delete($oldFile);
                }
            }
        });

        static::deleting(function (Product $product) {
            // Delete thumbnail from public storage
            if ($product->thumbnail_url && Storage::disk('public')->exists($product->thumbnail_url)) {
                Storage::disk('public')->delete($product->thumbnail_url);
            }

            // Delete file from local/private storage
            if ($product->file_url && Storage::disk('local')->exists($product->file_url)) {
                Storage::disk('local')->delete($product->file_url);
            }
        });
    }

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

    public function getTotalPurchasesAttribute(): int
    {
        return $this->orderItems()->whereHas('order', function($query) {
            $query->where('status', 'paid');
        })->sum('quantity');
    }
}
