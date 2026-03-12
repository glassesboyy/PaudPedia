<?php

namespace App\Http\Resources\Api\V1\Public\Landing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Landing-specific Webinar Resource.
 *
 * Optimized payload for the landing page — only includes fields
 * actually rendered by the WebinarCard component.
 *
 * Excluded vs full WebinarResource: scheduled_at, created_at
 */
class LandingWebinarResource extends JsonResource
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
            'scheduled_date' => $this->scheduled_at?->format('d M Y'),
            'scheduled_time' => $this->scheduled_at?->format('H:i'),
            'duration_minutes' => $this->duration_minutes,
            'max_participants' => $this->max_participants,
            'is_upcoming' => $this->isUpcoming(),
            'mentor' => $this->whenLoaded('mentor', function () {
                return [
                    'id' => $this->mentor->id,
                    'name' => $this->mentor->name,
                    'title' => $this->mentor->title,
                    'photo_url' => $this->mentor->photo_url ? asset('storage/' . $this->mentor->photo_url) : null,
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
