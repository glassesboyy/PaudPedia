<?php

namespace App\Http\Resources\Api\V1\School;

use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'avatar_url' => $this->user->avatar_url ? asset('storage/' . $this->user->avatar_url) : null,
            'nip' => $this->nip,
            'specialization' => $this->specialization,
            'bio' => $this->bio,
            'is_active' => $this->user->is_active,
            'homeroom_classes' => ClassRoomResource::collection($this->whenLoaded('homeroomClasses')),
            'joined_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
