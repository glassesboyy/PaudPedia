<?php

namespace App\Http\Resources\Api\V1\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * User Webinar Resource — registered webinar with zoom link.
 *
 * Used in: GET /api/v1/user/webinars
 * Wraps an OrderItem of type=webinar, enriched with the related Webinar model.
 */
class UserWebinarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $webinar = $this->item;

        return [
            'id' => $this->id,
            'webinar_id' => $webinar?->id,
            'title' => $this->item_title,
            'slug' => $webinar?->slug,
            'thumbnail_url' => $webinar?->thumbnail_url ? asset('storage/' . $webinar->thumbnail_url) : null,
            'scheduled_at' => $webinar?->scheduled_at?->toIso8601String(),
            'scheduled_date' => $webinar?->scheduled_at?->format('d M Y'),
            'scheduled_time' => $webinar?->scheduled_at?->format('H:i'),
            'duration_minutes' => $webinar?->duration_minutes,
            'status' => $this->getWebinarStatus($webinar),
            'status_label' => $this->getWebinarStatusLabel($webinar),
            'zoom_link' => $webinar?->zoom_link,
            'mentor' => $webinar?->relationLoaded('mentor') && $webinar?->mentor ? [
                'id' => $webinar->mentor->id,
                'name' => $webinar->mentor->name,
                'photo_url' => $webinar->mentor->photo_url ? asset('storage/' . $webinar->mentor->photo_url) : null,
            ] : null,
        ];
    }

    protected function getWebinarStatus($webinar): string
    {
        if (!$webinar || !$webinar->scheduled_at) {
            return 'unknown';
        }

        return $webinar->scheduled_at->isFuture() ? 'upcoming' : 'finished';
    }

    protected function getWebinarStatusLabel($webinar): string
    {
        return match ($this->getWebinarStatus($webinar)) {
            'upcoming' => 'Akan Datang',
            'finished' => 'Selesai',
            default => 'Tidak Diketahui',
        };
    }
}
