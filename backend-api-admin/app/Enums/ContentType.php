<?php

namespace App\Enums;

enum ContentType: string
{
    case VIDEO = 'video';
    case PDF = 'pdf';
    case TEXT = 'text';

    /**
     * Get content type display name
     */
    public function label(): string
    {
        return match($this) {
            self::VIDEO => 'Video',
            self::PDF => 'Dokumen PDF',
            self::TEXT => 'Teks',
        };
    }

    /**
     * Get content type color for UI
     */
    public function color(): string
    {
        return match($this) {
            self::VIDEO => 'danger',
            self::PDF => 'warning',
            self::TEXT => 'primary',
        };
    }

    /**
     * Get content type icon
     */
    public function icon(): string
    {
        return match($this) {
            self::VIDEO => 'play-circle',
            self::PDF => 'file-text',
            self::TEXT => 'align-left',
        };
    }

    /**
     * Check if content requires URL
     */
    public function requiresUrl(): bool
    {
        return $this === self::VIDEO;
    }

    /**
     * Check if content requires file upload
     */
    public function requiresFileUpload(): bool
    {
        return $this === self::PDF;
    }

    /**
     * Check if content requires rich text
     */
    public function requiresRichText(): bool
    {
        return $this === self::TEXT;
    }

    /**
     * Get allowed file extensions
     */
    public function allowedExtensions(): array
    {
        return match($this) {
            self::VIDEO => ['mp4', 'webm', 'youtube'],
            self::PDF => ['pdf'],
            self::TEXT => ['html', 'md'],
        };
    }

    /**
     * Get MIME types
     */
    public function mimeTypes(): array
    {
        return match($this) {
            self::VIDEO => ['video/mp4', 'video/webm'],
            self::PDF => ['application/pdf'],
            self::TEXT => ['text/html', 'text/markdown'],
        };
    }

    /**
     * Get all content type names
     */
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    /**
     * Get all content type values
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
