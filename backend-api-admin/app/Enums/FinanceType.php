<?php

namespace App\Enums;

enum FinanceType: string
{
    case SPP = 'spp';
    case TABUNGAN = 'tabungan';

    /**
     * Get type display name
     */
    public function label(): string
    {
        return match($this) {
            self::SPP => 'SPP (Pembayaran Bulanan)',
            self::TABUNGAN => 'Tabungan',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::SPP => 'SPP',
            self::TABUNGAN => 'Tabungan',
        };
    }

    /**
     * Get type description
     */
    public function description(): string
    {
        return match($this) {
            self::SPP => 'Pembayaran Sumbangan Pembinaan Pendidikan bulanan',
            self::TABUNGAN => 'Pencatatan tabungan siswa',
        };
    }

    /**
     * Get type color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::SPP => 'primary',
            self::TABUNGAN => 'success',
        };
    }

    /**
     * Get type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::SPP => 'credit-card',
            self::TABUNGAN => 'piggy-bank',
        };
    }

    /**
     * Check if type is recurring (monthly)
     */
    public function isRecurring(): bool
    {
        return $this === self::SPP;
    }

    /**
     * Check if type requires Pro plan
     */
    public function requiresProPlan(): bool
    {
        return true; // Both finance features require Pro plan
    }

    /**
     * Get default amount (if any)
     */
    public function defaultAmount(): ?int
    {
        return match($this) {
            self::SPP => null, // Set per school
            self::TABUNGAN => null,
        };
    }

    /**
     * Get all type names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
