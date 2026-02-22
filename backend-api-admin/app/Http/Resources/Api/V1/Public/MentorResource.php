<?php

namespace App\Http\Resources\Api\V1\Public;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentorResource extends JsonResource
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
            'bio' => $this->getBioExcerpt(),
            'photo_url' => $this->photo_url ? asset('storage/' . $this->photo_url) : null,
            'expertise' => $this->expertise,
            'courses_count' => $this->whenCounted('courses', $this->courses_count ?? 0),
            'webinars_count' => $this->whenCounted('webinars', $this->webinars_count ?? 0),
        ];
    }

    /**
     * Get bio excerpt.
     *
     * @return string|null
     */
    protected function getBioExcerpt(): ?string
    {
        if (!$this->bio) {
            return null;
        }

        return strlen($this->bio) > 150 ? substr($this->bio, 0, 150) . '...' : $this->bio;
    }
}
