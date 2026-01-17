<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_amount',
        'discount_amount',
        'final_amount',
        'promo_code', // String, not FK
        'status',
        'payment_method', // String from Midtrans
        'payment_url', // Midtrans Snap URL
        'midtrans_order_id',
        'midtrans_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
        'status' => OrderStatus::class,
        'paid_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('status', OrderStatus::PAID);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', OrderStatus::CANCELLED);
    }

    public function scopeExpired($query)
    {
        return $query->where('status', OrderStatus::EXPIRED);
    }

    public function scopeByStatus($query, OrderStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPaymentMethod($query, PaymentMethod $method)
    {
        return $query->where('payment_method', $method);
    }

    // Helper Methods
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));
        
        return "{$prefix}-{$date}-{$random}";
    }

    public function isPaid(): bool
    {
        return $this->status === OrderStatus::PAID;
    }

    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === OrderStatus::EXPIRED || 
               ($this->expired_at && $this->expired_at->isPast());
    }

    public function markAsPaid(): void
    {
        $this->status = OrderStatus::PAID;
        $this->payment_status = PaymentStatus::PAID;
        $this->paid_at = now();
        $this->save();

        // TODO: Process order (create enrollments, send notifications, etc.)
    }

    public function markAsCancelled(): void
    {
        $this->status = OrderStatus::CANCELLED;
        $this->save();
    }

    public function markAsExpired(): void
    {
        $this->status = OrderStatus::EXPIRED;
        $this->save();
    }

    public function calculateTotal(): void
    {
        $this->total_amount = $this->items()->sum('subtotal');
        
        // Apply discount if promo code exists
        $discount = 0;
        if ($this->promoCode && $this->promoCode->isValid()) {
            $discount = $this->promoCode->calculateDiscount($this->total_amount);
        }
        
        $this->discount_amount = $discount;
        $this->final_amount = $this->total_amount - $discount;
        
        $this->save();
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getFormattedFinalAttribute(): string
    {
        return 'Rp ' . number_format($this->final_amount, 0, ',', '.');
    }
}
