<?php

namespace App\Models;

use App\Enums\SubscriptionOrderStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriptionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'amount',
        'status',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'snap_token',
        'payment_method',
        'duration_months',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'status' => SubscriptionOrderStatus::class,
        'duration_months' => 'integer',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    // Relationships
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // Scopes
    public function scopePending(Builder $query)
    {
        return $query->where('status', SubscriptionOrderStatus::PENDING);
    }

    public function scopePaid(Builder $query)
    {
        return $query->where('status', SubscriptionOrderStatus::PAID);
    }

    // Helpers
    public function isPending(): bool
    {
        return $this->status === SubscriptionOrderStatus::PENDING;
    }

    public function isPaid(): bool
    {
        return $this->status === SubscriptionOrderStatus::PAID;
    }

    public function markAsPaid(?string $transactionId = null, ?string $paymentMethod = null): void
    {
        $this->update([
            'status' => SubscriptionOrderStatus::PAID,
            'midtrans_transaction_id' => $transactionId,
            'payment_method' => $paymentMethod,
            'paid_at' => now(),
        ]);
    }

    public function markAsFailed(): void
    {
        $this->update(['status' => SubscriptionOrderStatus::FAILED]);
    }

    public function markAsExpired(): void
    {
        $this->update(['status' => SubscriptionOrderStatus::EXPIRED]);
    }

    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
