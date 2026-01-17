<?php

namespace App\Models;

use App\Enums\OrderItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_type',
        'item_id',
        'item_title', // Snapshot
        'item_price', // Snapshot
        'quantity',
        'subtotal',
    ];

    protected $casts = [
        'item_type' => OrderItemType::class,
        'quantity' => 'integer',
        'item_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }

    // Helper Methods
    public function calculateSubtotal(): void
    {
        $this->subtotal = $this->item_price * $this->quantity;
        $this->save();
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->item_price, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function isCourse(): bool
    {
        return $this->item_type === OrderItemType::COURSE;
    }

    public function isWebinar(): bool
    {
        return $this->item_type === OrderItemType::WEBINAR;
    }

    public function isProduct(): bool
    {
        return $this->item_type === OrderItemType::PRODUCT;
    }
}
