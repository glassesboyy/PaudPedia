<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case SETTLEMENT = 'settlement';
    case CAPTURE = 'capture';
    case DENY = 'deny';
    case CANCEL = 'cancel';
    case EXPIRE = 'expire';
    case FAILURE = 'failure';
    case PAID = 'paid';

    /**
     * Get status display name
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Pembayaran',
            self::SETTLEMENT => 'Berhasil',
            self::CAPTURE => 'Berhasil (Capture)',
            self::DENY => 'Ditolak',
            self::CANCEL => 'Dibatalkan',
            self::EXPIRE => 'Kadaluarsa',
            self::FAILURE => 'Gagal',
            self::PAID => 'Lunas',
        };
    }

    /**
     * Get status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::SETTLEMENT, self::CAPTURE, self::PAID => 'success',
            self::DENY, self::FAILURE => 'danger',
            self::CANCEL, self::EXPIRE => 'secondary',
        };
    }

    /**
     * Get status icon
     */
    public function icon(): string
    {
        return match($this) {
            self::PENDING => 'clock',
            self::SETTLEMENT, self::CAPTURE => 'check-circle',
            self::DENY, self::FAILURE => 'x-circle',
            self::CANCEL => 'slash-circle',
            self::EXPIRE => 'calendar-x',
        };
    }

    /**
     * Check if payment is successful
     */
    public function isSuccessful(): bool
    {
        return in_array($this, [self::SETTLEMENT, self::CAPTURE]);
    }

    /**
     * Check if payment is failed
     */
    public function isFailed(): bool
    {
        return in_array($this, [self::DENY, self::FAILURE, self::EXPIRE]);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Check if payment is cancelled
     */
    public function isCancelled(): bool
    {
        return $this === self::CANCEL;
    }

    /**
     * Convert to OrderStatus
     */
    public function toOrderStatus(): OrderStatus
    {
        return match($this) {
            self::PENDING => OrderStatus::PENDING,
            self::SETTLEMENT, self::CAPTURE => OrderStatus::PAID,
            self::DENY, self::FAILURE, self::EXPIRE => OrderStatus::FAILED,
            self::CANCEL => OrderStatus::CANCELLED,
        };
    }

    /**
     * Get Midtrans status mapping
     */
    public static function fromMidtrans(string $status): self
    {
        return match(strtolower($status)) {
            'pending' => self::PENDING,
            'settlement' => self::SETTLEMENT,
            'capture' => self::CAPTURE,
            'deny' => self::DENY,
            'cancel' => self::CANCEL,
            'expire' => self::EXPIRE,
            'failure' => self::FAILURE,
            default => self::PENDING,
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
