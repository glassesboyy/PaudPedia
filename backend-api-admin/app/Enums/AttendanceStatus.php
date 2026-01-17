<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case PRESENT = 'present';
    case SICK = 'sick';
    case PERMISSION = 'permission';
    case ABSENT = 'absent';

    /**
     * Get status display name
     */
    public function label(): string
    {
        return match($this) {
            self::PRESENT => 'Hadir',
            self::SICK => 'Sakit',
            self::PERMISSION => 'Izin',
            self::ABSENT => 'Alfa',
        };
    }

    /**
     * Get status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::PRESENT => 'success',
            self::SICK => 'warning',
            self::PERMISSION => 'info',
            self::ABSENT => 'danger',
        };
    }

    /**
     * Get status icon
     */
    public function icon(): string
    {
        return match($this) {
            self::PRESENT => 'check-circle',
            self::SICK => 'heart-pulse',
            self::PERMISSION => 'file-text',
            self::ABSENT => 'x-circle',
        };
    }

    /**
     * Check if status counts as present (for attendance percentage)
     */
    public function countsAsPresent(): bool
    {
        return $this === self::PRESENT;
    }

    /**
     * Check if status requires notes
     */
    public function requiresNotes(): bool
    {
        return in_array($this, [self::SICK, self::PERMISSION]);
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
