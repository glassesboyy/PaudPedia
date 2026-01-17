<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'mentor_id',
        'title',
        'slug',
        'description',
        'thumbnail_url',
        'price',
        'original_price',
        'zoom_link',
        'zoom_meeting_id',
        'zoom_passcode',
        'scheduled_at',
        'duration_minutes',
        'max_participants',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'max_participants' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function mentor(): BelongsTo
    {
        return $this->belongsTo(Mentor::class);
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

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('scheduled_at', '<=', now());
    }

    // Helper Methods
    public function isUpcoming(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    public function isPast(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isPast();
    }

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
        })->count();
    }
}
