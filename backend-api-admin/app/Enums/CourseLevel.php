<?php

namespace App\Enums;

enum CourseLevel: string
{
    case BEGINNER = 'beginner';
    case INTERMEDIATE = 'intermediate';
    case ADVANCED = 'advanced';

    /**
     * Get level display name
     */
    public function label(): string
    {
        return match($this) {
            self::BEGINNER => 'Pemula',
            self::INTERMEDIATE => 'Menengah',
            self::ADVANCED => 'Lanjutan',
        };
    }

    /**
     * Get level color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::BEGINNER => 'success',
            self::INTERMEDIATE => 'warning',
            self::ADVANCED => 'danger',
        };
    }

    /**
     * Get level icon
     */
    public function icon(): string
    {
        return match($this) {
            self::BEGINNER => 'award',
            self::INTERMEDIATE => 'star',
            self::ADVANCED => 'crown',
        };
    }

    /**
     * Get level description
     */
    public function description(): string
    {
        return match($this) {
            self::BEGINNER => 'Cocok untuk yang baru memulai',
            self::INTERMEDIATE => 'Membutuhkan pengetahuan dasar',
            self::ADVANCED => 'Untuk yang sudah berpengalaman',
        };
    }

    /**
     * Get difficulty rating (1-5)
     */
    public function difficultyRating(): int
    {
        return match($this) {
            self::BEGINNER => 1,
            self::INTERMEDIATE => 3,
            self::ADVANCED => 5,
        };
    }

    /**
     * Get all level names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all level values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
