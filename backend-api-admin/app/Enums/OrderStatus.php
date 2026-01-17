<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELLED = 'cancelled';
    case EXPIRED = 'expired';

    /**
     * Get status display name
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::PAID => 'Lunas',
            self::FAILED => 'Gagal',
            self::CANCELLED => 'Dibatalkan',
            self::EXPIRED => 'Kadaluarsa',
        };
    }

    /**
     * Get status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PAID => 'success',
            self::FAILED => 'danger',
            self::CANCELLED => 'secondary',
            self::EXPIRED => 'secondary',
        };
    }

    /**
     * Get status icon
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::PAID => 'check-circle',
            self::FAILED => 'x-circle',
            self::CANCELLED => 'slash-circle',
        };
    }

    /**
     * Check if status is final (cannot be changed)
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::PAID, self::FAILED, self::CANCELLED]);
    }

    /**
     * Check if status allows payment
     */
    public function allowsPayment(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if status grants access to content
     */
    public function grantsAccess(): bool
    {
        return $this === self::PAID;
    }

    /**
     * Check if status can be cancelled
     */
    public function canBeCancelled(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Get next possible statuses
     */
    public function nextStatuses(): array
    {
        return match($this) {
            self::PENDING => [self::PAID, self::FAILED, self::CANCELLED],
            self::PAID => [],
            self::FAILED => [],
            self::CANCELLED => [],
        };
    }

    /**
     * Get all status names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all status values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
