<?php

namespace App\Enums;

enum CategoryType: string
{
    case COURSE = 'course';
    case PRODUCT = 'product';
    case ARTICLE = 'article';

    /**
     * Get category type display name
     */
    public function label(): string
    {
        return match($this) {
            self::COURSE => 'Kategori Kursus',
            self::PRODUCT => 'Kategori Produk',
            self::ARTICLE => 'Kategori Artikel',
        };
    }

    /**
     * Get short label
     */
    public function shortLabel(): string
    {
        return match($this) {
            self::COURSE => 'Kursus',
            self::PRODUCT => 'Produk',
            self::ARTICLE => 'Artikel',
        };
    }

    /**
     * Get category type color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::COURSE => 'success',
            self::PRODUCT => 'warning',
            self::ARTICLE => 'primary',
        };
    }

    /**
     * Get category type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::COURSE => 'book-open',
            self::PRODUCT => 'shopping-bag',
            self::ARTICLE => 'file-text',
        };
    }

    /**
     * Get model class
     */
    public function modelClass(): string
    {
        return match($this) {
            self::COURSE => \App\Models\Course::class,
            self::PRODUCT => \App\Models\Product::class,
            self::ARTICLE => \App\Models\Article::class,
        };
    }

    /**
     * Get description
     */
    public function description(): string
    {
        return match($this) {
            self::COURSE => 'Kategori untuk mengelompokkan kursus online',
            self::PRODUCT => 'Kategori untuk mengelompokkan produk digital',
            self::ARTICLE => 'Kategori untuk mengelompokkan artikel blog',
        };
    }

    /**
     * Get example categories
     */
    public function exampleCategories(): array
    {
        return match($this) {
            self::COURSE => [
                'Parenting',
                'Pendidikan Anak',
                'Kesehatan Anak',
                'Tumbuh Kembang',
            ],
            self::PRODUCT => [
                'E-Book',
                'Template',
                'Worksheet',
                'Aktivitas Anak',
            ],
            self::ARTICLE => [
                'Tips Parenting',
                'Kesehatan',
                'Pendidikan',
                'Aktivitas',
                'Nutrisi',
            ],
        };
    }

    /**
     * Get all category type names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all category type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
