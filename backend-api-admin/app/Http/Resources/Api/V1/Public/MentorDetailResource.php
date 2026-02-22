<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorDetailResource extends JsonResource
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
            'name' => $this->name,
            'full_title' => $this->full_title,
            'title' => $this->title,
            'bio' => $this->bio,
            'photo_url' => $this->photo_url ? asset('storage/' . $this->photo_url) : null,
            'expertise' => $this->expertise,
            'social_media' => $this->social_media ?? [],
            'courses_count' => $this->whenCounted('courses', $this->courses_count ?? 0),
            'webinars_count' => $this->whenCounted('webinars', $this->webinars_count ?? 0),
            'courses' => $this->whenLoaded('courses', function () {
                return $this->courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'slug' => $course->slug,
                        'thumbnail_url' => $course->thumbnail_url ? asset('storage/' . $course->thumbnail_url) : null,
                        'price' => (float) $course->price,
                        'level' => $course->level?->value,
                        'category' => [
                            'id' => $course->category?->id,
                            'name' => $course->category?->name,
                            'slug' => $course->category?->slug,
                        ],
                    ];
                });
            }),
            'webinars' => $this->whenLoaded('webinars', function () {
                return $this->webinars->map(function ($webinar) {
                    return [
                        'id' => $webinar->id,
                        'title' => $webinar->title,
                        'slug' => $webinar->slug,
                        'thumbnail_url' => $webinar->thumbnail_url ? asset('storage/' . $webinar->thumbnail_url) : null,
                        'price' => (float) $webinar->price,
                        'scheduled_at' => $webinar->scheduled_at?->toIso8601String(),
                        'scheduled_date' => $webinar->scheduled_at?->format('d M Y'),
                    ];
                });
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
