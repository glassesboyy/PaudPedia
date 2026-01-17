<?php

namespace App\Enums;

enum DiscountType: string
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';

    /**
     * Get discount type display name
     */
    public function label(): string
    {
        return match($this) {
            self::PERCENTAGE => 'Persentase (%)',
            self::FIXED => 'Nominal Tetap (Rp)',
        };
    }

    /**
     * Get discount type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::PERCENTAGE => 'percent',
            self::FIXED => 'dollar-sign',
        };
    }

    /**
     * Calculate discount amount
     * 
     * @param float $value Discount value (percentage or fixed amount)
     * @param float $originalPrice Original price
     * @param float|null $maxDiscount Maximum discount (for percentage type)
     * @return float
     */
    public function calculateDiscount(float $value, float $originalPrice, ?float $maxDiscount = null): float
    {
        return match($this) {
            self::PERCENTAGE => min(
                ($originalPrice * $value / 100),
                $maxDiscount ?? PHP_FLOAT_MAX
            ),
            self::FIXED => min($value, $originalPrice),
        };
    }

    /**
     * Format discount display
     * 
     * @param float $value Discount value
     * @return string
     */
    public function format(float $value): string
    {
        return match($this) {
            self::PERCENTAGE => number_format($value, 0) . '%',
            self::FIXED => 'Rp ' . number_format($value, 0, ',', '.'),
        };
    }

    /**
     * Validate discount value
     * 
     * @param float $value Discount value
     * @return bool
     */
    public function isValid(float $value): bool
    {
        return match($this) {
            self::PERCENTAGE => $value > 0 && $value <= 100,
            self::FIXED => $value > 0,
        };
    }

    /**
     * Get validation rules
     */
    public function validationRules(): array
    {
        return match($this) {
            self::PERCENTAGE => ['numeric', 'min:1', 'max:100'],
            self::FIXED => ['numeric', 'min:1'],
        };
    }

    /**
     * Get all discount type names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all discount type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
