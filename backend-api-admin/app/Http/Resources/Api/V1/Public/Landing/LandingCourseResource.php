<?php

namespace App\Http\Resources\Api\V1\Public\Landing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Landing-specific Course Resource.
 *
 * Optimized payload for the landing page — only includes fields
 * actually rendered by the CourseCard component.
 *
 * Excluded vs full CourseResource: created_at
 * Category: only id + name (slug not used on card)
 */
class LandingCourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->excerpt ?? $this->getExcerpt(),
            'thumbnail_url' => $this->thumbnail_url ? asset('storage/' . $this->thumbnail_url) : null,
            'price' => (float) $this->price,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'has_discount' => $this->hasDiscount(),
            'discount_percentage' => $this->discount_percentage,
            'level' => $this->level?->value,
            'level_label' => $this->level?->label(),
            'duration_hours' => $this->duration_hours,
            'modules_count' => $this->whenCounted('modules', $this->modules_count ?? 0),
            'mentor' => $this->whenLoaded('mentor', function () {
                return [
                    'id' => $this->mentor->id,
                    'name' => $this->mentor->name,
                    'title' => $this->mentor->title,
                    'photo_url' => $this->mentor->photo_url ? asset('storage/' . $this->mentor->photo_url) : null,
                ];
            }),
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
}
