<?php

namespace App\Enums;

enum Semester: string
{
    case SEMESTER_1 = '1';
    case SEMESTER_2 = '2';

    /**
     * Get semester display name
     */
    public function label(): string
    {
        return match($this) {
            self::SEMESTER_1 => 'Semester 1',
            self::SEMESTER_2 => 'Semester 2',
        };
    }

    /**
     * Get semester short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::SEMESTER_1 => 'Sem 1',
            self::SEMESTER_2 => 'Sem 2',
        };
    }

    /**
     * Get semester description
     */
    public function description(): string
    {
        return match($this) {
            self::SEMESTER_1 => 'Semester 1 (Juli - Desember)',
            self::SEMESTER_2 => 'Semester 2 (Januari - Juni)',
        };
    }

    /**
     * Get semester color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::SEMESTER_1 => 'primary',
            self::SEMESTER_2 => 'success',
        };
    }

    /**
     * Get semester months
     */
    public function months(): array
    {
        return match($this) {
            self::SEMESTER_1 => [7, 8, 9, 10, 11, 12], // July - December
            self::SEMESTER_2 => [1, 2, 3, 4, 5, 6],    // January - June
        };
    }

    /**
     * Get semester start month
     */
    public function startMonth(): int
    {
        return match($this) {
            self::SEMESTER_1 => 7,  // July
            self::SEMESTER_2 => 1,  // January
        };
    }

    /**
     * Get semester end month
     */
    public function endMonth(): int
    {
        return match($this) {
            self::SEMESTER_1 => 12, // December
            self::SEMESTER_2 => 6,  // June
        };
    }

    /**
     * Determine semester from date
     */
    public static function fromDate(\DateTimeInterface $date): self
    {
        $month = (int) $date->format('n');
        
        return in_array($month, [1, 2, 3, 4, 5, 6]) 
            ? self::SEMESTER_2 
            : self::SEMESTER_1;
    }

    /**
     * Get current semester
     */
    public static function current(): self
    {
        return self::fromDate(new \DateTime());
    }

    /**
     * Get all semester names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all semester values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
