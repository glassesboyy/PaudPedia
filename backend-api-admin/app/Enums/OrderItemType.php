<?php

namespace App\Enums;

enum OrderItemType: string
{
    case WEBINAR = 'webinar';
    case COURSE = 'course';
    case PRODUCT = 'product';

    /**
     * Get item type display name
     */
    public function label(): string
    {
        return match($this) {
            self::WEBINAR => 'Webinar',
            self::COURSE => 'Kursus',
            self::PRODUCT => 'Produk Digital',
        };
    }

    /**
     * Get item type color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::WEBINAR => 'primary',
            self::COURSE => 'success',
            self::PRODUCT => 'warning',
        };
    }

    /**
     * Get item type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::WEBINAR => 'video',
            self::COURSE => 'book-open',
            self::PRODUCT => 'download',
        };
    }

    /**
     * Get model class for polymorphic relationship
     */
    public function modelClass(): string
    {
        return match($this) {
            self::WEBINAR => \App\Models\Webinar::class,
            self::COURSE => \App\Models\Course::class,
            self::PRODUCT => \App\Models\Product::class,
        };
    }

    /**
     * Check if item type grants access after purchase
     */
    public function grantsAccess(): bool
    {
        return in_array($this, [self::WEBINAR, self::COURSE]);
    }

    /**
     * Check if item type is downloadable
     */
    public function isDownloadable(): bool
    {
        return $this === self::PRODUCT;
    }

    /**
     * Check if item type requires enrollment
     */
    public function requiresEnrollment(): bool
    {
        return $this === self::COURSE;
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match($this) {
            self::WEBINAR => 'Akses ke live webinar via Zoom',
            self::COURSE => 'Akses lifetime ke kursus online',
            self::PRODUCT => 'Download produk digital',
        };
    }

    /**
     * Get all item type names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all item type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
