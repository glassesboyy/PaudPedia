<?php

namespace App\Http\Resources\Api\V1\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'class_id' => $this->class_id,
            'parent_profile_id' => $this->parent_profile_id,
            'name' => $this->name,
            'nisn' => $this->nisn,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'gender' => $this->gender?->value ?? $this->gender,
            'address' => $this->address,
            'photo_url' => $this->photo_url
                ? asset('storage/' . $this->photo_url)
                : null,
            'enrollment_date' => $this->enrollment_date?->format('Y-m-d'),
            'status' => $this->status?->value ?? $this->status,
            'parent' => new ParentProfileResource($this->whenLoaded('parent')),
            'class' => new ClassRoomResource($this->whenLoaded('class')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
