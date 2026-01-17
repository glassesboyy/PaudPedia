<?php

namespace App\Enums;

enum StudentStatus: string
{
    case ACTIVE = 'active';
    case GRADUATED = 'graduated';
    case TRANSFERRED = 'transferred';

    /**
     * Get status display name
     */
    public function label(): string
    {
        return match($this) {
            self::ACTIVE => 'Aktif',
            self::GRADUATED => 'Lulus',
            self::TRANSFERRED => 'Pindah',
        };
    }

    /**
     * Get status color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::ACTIVE => 'success',
            self::GRADUATED => 'primary',
            self::TRANSFERRED => 'warning',
        };
    }

    /**
     * Get status icon
     */
    public function icon(): string
    {
        return match($this) {
            self::ACTIVE => 'check-circle',
            self::GRADUATED => 'award',
            self::TRANSFERRED => 'arrow-right-circle',
        };
    }

    /**
     * Get status description
     */
    public function description(): string
    {
        return match($this) {
            self::ACTIVE => 'Siswa masih aktif bersekolah',
            self::GRADUATED => 'Siswa telah lulus',
            self::TRANSFERRED => 'Siswa telah pindah sekolah',
        };
    }

    /**
     * Check if status is active
     */
    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if status allows attendance input
     */
    public function allowsAttendance(): bool
    {
        return $this === self::ACTIVE;
    }

    /**
     * Check if status allows assessment input
     */
    public function allowsAssessment(): bool
    {
        return $this === self::ACTIVE;
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
