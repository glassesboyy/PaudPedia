<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebinarDetailResource extends JsonResource
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
            'description' => $this->description,
            'thumbnail_url' => $this->thumbnail_url ? asset('storage/' . $this->thumbnail_url) : null,
            'price' => (float) $this->price,
            'original_price' => $this->original_price ? (float) $this->original_price : null,
            'has_discount' => $this->hasDiscount(),
            'discount_percentage' => $this->discount_percentage,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'scheduled_date' => $this->scheduled_at?->format('d M Y'),
            'scheduled_time' => $this->scheduled_at?->format('H:i'),
            'scheduled_day' => $this->scheduled_at?->translatedFormat('l'),
            'duration_minutes' => $this->duration_minutes,
            'max_participants' => $this->max_participants,
            'is_upcoming' => $this->isUpcoming(),
            'is_past' => $this->isPast(),
            'mentor' => $this->whenLoaded('mentor', function () {
                return [
                    'id' => $this->mentor->id,
                    'name' => $this->mentor->name,
                    'title' => $this->mentor->title,
                    'bio' => $this->mentor->bio,
                    'expertise' => $this->mentor->expertise,
                    'photo_url' => $this->mentor->photo_url ? asset('storage/' . $this->mentor->photo_url) : null,
                    'social_media' => $this->mentor->social_media,
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }

    /**
     * Check if webinar has discount.
     *
     * @return bool
     */
    protected function hasDiscount(): bool
    {
        return $this->original_price && $this->original_price > $this->price;
    }
}
