<?php

namespace App\Http\Resources\Api\V1\Public\Landing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/**
 * Landing-specific Product Resource.
 *
 * Optimized payload for the landing page — only includes fields
 * actually rendered by the ProductCard component.
 *
 * Excluded vs full ProductResource: created_at
 * Category: only id + name (slug not used on card)
 */
class LandingProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->getExcerpt(),
            'thumbnail_url' => $this->thumbnail_url ? asset('storage/' . $this->thumbnail_url) : null,
            'price' => (float) $this->price,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'has_discount' => $this->hasDiscount(),
            'discount_percentage' => $this->discount_percentage,
            'file_info' => $this->getFileInfo(),
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                ];
            }),
        ];
    }

    protected function getExcerpt(): ?string
    {
        if (!$this->description) {
            return null;
        }

        $plainText = strip_tags($this->description);
        return strlen($plainText) > 150 ? substr($plainText, 0, 150) . '...' : $plainText;
    }

    protected function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    protected function getFileInfo(): ?array
    {
        if (!$this->file_url) {
            return null;
        }

        $extension = pathinfo($this->file_url, PATHINFO_EXTENSION);
        $size = null;

        if (Storage::disk('local')->exists($this->file_url)) {
            $size = Storage::disk('local')->size($this->file_url);
        }

        return [
            'type' => strtoupper($extension),
            'size' => $size,
            'size_formatted' => $size ? $this->formatBytes($size) : null,
        ];
    }

    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
