<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
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
                    'slug' => $this->category->slug,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }

    /**
     * Get excerpt from description.
     *
     * @return string|null
     */
    protected function getExcerpt(): ?string
    {
        if (!$this->description) {
            return null;
        }

        $plainText = strip_tags($this->description);
        return strlen($plainText) > 150 ? substr($plainText, 0, 150) . '...' : $plainText;
    }

    /**
     * Check if course has discount.
     *
     * @return bool
     */
    protected function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }
}
