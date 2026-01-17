<?php

namespace App\Enums;

enum Gender: string
{
    case MALE = 'male';
    case FEMALE = 'female';

    /**
     * Get gender display name
     */
    public function label(): string
    {
        return match($this) {
            self::MALE => 'Laki-laki',
            self::FEMALE => 'Perempuan',
        };
    }

    /**
     * Get gender color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::MALE => 'blue',
            self::FEMALE => 'pink',
        };
    }

    /**
     * Get gender icon
     */
    public function icon(): string
    {
        return match($this) {
            self::MALE => 'mars',
            self::FEMALE => 'venus',
        };
    }

    /**
     * Get pronoun (dia/beliau)
     */
    public function pronoun(): string
    {
        return match($this) {
            self::MALE => 'dia',
            self::FEMALE => 'dia',
        };
    }

    /**
     * Get all gender names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all gender values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
