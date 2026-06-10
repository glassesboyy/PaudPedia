<?php

namespace App\Enums;

enum TransactionType: string
{
    case DEPOSIT = 'deposit';
    case WITHDRAWAL = 'withdrawal';

    /**
     * Get type display label
     */
    public function label(): string
    {
        return match($this) {
            self::DEPOSIT => 'Setoran',
            self::WITHDRAWAL => 'Penarikan',
        };
    }

    /**
     * Get type color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::DEPOSIT => 'success',
            self::WITHDRAWAL => 'danger',
        };
    }

    /**
     * Get type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::DEPOSIT => 'arrow-down-circle',
            self::WITHDRAWAL => 'arrow-up-circle',
        };
    }
}
