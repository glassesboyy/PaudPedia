<?php

namespace App\Models;

use App\Enums\OrderItemType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'item_type',
        'item_id',
        'quantity',
    ];

    protected $casts = [
        'item_type' => OrderItemType::class,
        'quantity' => 'integer',
    ];

    // Relationships

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function item(): MorphTo
    {
        return $this->morphTo('item', 'item_type', 'item_id');
    }

    // Helpers

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
