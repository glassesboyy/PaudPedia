<?php

namespace App\Enums;

enum SubscriptionPlan: string
{
    case FREE = 'free';
    case PRO = 'pro';

    /**
     * Get student limit for the plan
     */
    public function studentLimit(): ?int
    {
        return match($this) {
            self::FREE => 20,
            self::PRO => null, // unlimited
        };
    }

    /**
     * Get teacher limit for the plan
     */
    public function teacherLimit(): ?int
    {
        return match($this) {
            self::FREE => 5,
            self::PRO => null, // unlimited
        };
    }

    /**
     * Get features available for the plan
     */
    public function features(): array
    {
        return match($this) {
            self::FREE => [
                'student_management',
                'teacher_management',
                'class_management',
                'attendance',
                'assessment',
            ],
            self::PRO => [
                'student_management',
                'teacher_management',
                'class_management',
                'attendance',
                'assessment',
                'pdf_report',
                'finance_management',
                'unlimited_students',
                'unlimited_teachers',
                'priority_support',
            ],
        };
    }

    /**
     * Check if plan has specific feature
     */
    public function hasFeature(string $feature): bool
    {
        return in_array($feature, $this->features());
    }

    /**
     * Get monthly price in IDR
     */
    public function monthlyPrice(): int
    {
        return match($this) {
            self::FREE => 0,
            self::PRO => 200000,
        };
    }

    /**
     * Get plan display name
     */
    public function label(): string
    {
        return match($this) {
            self::FREE => 'Gratis',
            self::PRO => 'Pro',
        };
    }

    /**
     * Get all plan names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all plan values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
