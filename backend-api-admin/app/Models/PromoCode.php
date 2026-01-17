<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PromoCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'usage_limit',
        'usage_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_type' => DiscountType::class,
        'discount_value' => 'decimal:2',
        'min_purchase_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where(function($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    public function scopeAvailable($query)
    {
        return $query->active()
                    ->where(function($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('usage_count < usage_limit');
                    });
    }

    // Helper Methods
    public function isValid(): bool
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check dates
        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    public function canBeUsedForAmount(float $amount): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        if ($this->min_purchase_amount && $amount < $this->min_purchase_amount) {
            return false;
        }

        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if (!$this->canBeUsedForAmount($amount)) {
            return 0;
        }

        $discount = match($this->discount_type) {
            DiscountType::PERCENTAGE => ($amount * $this->discount_value) / 100,
            DiscountType::FIXED => $this->discount_value,
            default => 0,
        };

        // Apply max discount limit for percentage discounts
        if ($this->discount_type === DiscountType::PERCENTAGE && $this->max_discount_amount) {
            $discount = min($discount, $this->max_discount_amount);
        }

        return round($discount, 2);
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function getRemainingUsesAttribute(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->usage_count);
    }

    public function getDiscountDisplayAttribute(): string
    {
        return match($this->discount_type) {
            DiscountType::PERCENTAGE => $this->discount_value . '%',
            DiscountType::FIXED => 'Rp ' . number_format($this->discount_value, 0, ',', '.'),
            default => '-',
        };
    }
}
