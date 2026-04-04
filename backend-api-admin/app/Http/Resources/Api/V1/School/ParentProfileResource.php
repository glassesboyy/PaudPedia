<?php

namespace App\Http\Resources\Api\V1\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParentProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'school_id' => $this->school_id,
            'user_id' => $this->user_id,
            'email' => $this->email,
            'father_name' => $this->father_name,
            'mother_name' => $this->mother_name,
            'phone' => $this->phone,
            'father_occupation' => $this->father_occupation,
            'mother_occupation' => $this->mother_occupation,
            'address' => $this->address,
            'children_count' => $this->whenCounted('children', $this->children()->count()),
            'children' => StudentResource::collection($this->whenLoaded('children')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
