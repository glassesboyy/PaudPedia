<?php

namespace App\Enums;

enum AssessmentScale: string
{
    case BB = 'BB';   // Belum Berkembang
    case MB = 'MB';   // Mulai Berkembang
    case BSH = 'BSH'; // Berkembang Sesuai Harapan
    case BSB = 'BSB'; // Berkembang Sangat Baik

    /**
     * Get scale display name (Indonesian)
     */
    public function label(): string
    {
        return match($this) {
            self::BB => 'Belum Berkembang',
            self::MB => 'Mulai Berkembang',
            self::BSH => 'Berkembang Sesuai Harapan',
            self::BSB => 'Berkembang Sangat Baik',
        };
    }

    /**
     * Get scale description
     */
    public function description(): string
    {
        return match($this) {
            self::BB => 'Anak belum mampu melakukan indikator penilaian',
            self::MB => 'Anak mulai dapat melakukan indikator dengan bantuan',
            self::BSH => 'Anak dapat melakukan indikator secara mandiri',
            self::BSB => 'Anak dapat melakukan indikator lebih dari yang diharapkan',
        };
    }

    /**
     * Get scale color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::BB => 'danger',
            self::MB => 'warning',
            self::BSH => 'success',
            self::BSB => 'primary',
        };
    }

    /**
     * Get numeric value (for calculations/sorting)
     */
    public function numericValue(): int
    {
        return match($this) {
            self::BB => 1,
            self::MB => 2,
            self::BSH => 3,
            self::BSB => 4,
        };
    }

    /**
     * Get percentage range
     */
    public function percentageRange(): array
    {
        return match($this) {
            self::BB => ['min' => 0, 'max' => 25],
            self::MB => ['min' => 26, 'max' => 50],
            self::BSH => ['min' => 51, 'max' => 75],
            self::BSB => ['min' => 76, 'max' => 100],
        };
    }

    /**
     * Get scale icon
     */
    public function icon(): string
    {
        return match($this) {
            self::BB => 'star',
            self::MB => 'star-half',
            self::BSH => 'star',
            self::BSB => 'stars',
        };
    }

    /**
     * Check if scale is passing
     */
    public function isPassing(): bool
    {
        return in_array($this, [self::BSH, self::BSB]);
    }

    /**
     * Get all scale names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all scale values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Create from numeric value
     */
    public static function fromNumeric(int $value): self
    {
        return match($value) {
            1 => self::BB,
            2 => self::MB,
            3 => self::BSH,
            4 => self::BSB,
            default => self::BB,
        };
    }
}
