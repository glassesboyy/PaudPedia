<?php

namespace App\Http\Resources\Api\V1\Public\Landing;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Landing-specific Testimonial Resource.
 *
 * Optimized payload for the landing page — only includes fields
 * actually rendered by the TestimonialCard component.
 *
 * Excluded vs full TestimonialResource: star_rating, is_featured, created_at
 */
class LandingTestimonialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->display_name,
            'title' => $this->title,
            'content' => $this->content,
            'rating' => $this->rating,
            'photo_url' => $this->photo_url ? asset('storage/' . $this->photo_url) : null,
        ];
    }
}
