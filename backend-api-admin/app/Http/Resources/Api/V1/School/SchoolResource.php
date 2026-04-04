<?php

namespace App\Http\Resources\Api\V1\School;

use Illuminate\Http\Resources\Json\JsonResource;

class SchoolResource extends JsonResource
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
            'name' => $this->name,
            'npsn' => $this->npsn,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'logo_url' => $this->getLogoUrl(),
            'subscription_plan' => $this->subscription_plan,
            'subscription_started_at' => $this->subscription_started_at?->toIso8601String(),
            'subscription_ended_at' => $this->subscription_ended_at?->toIso8601String(),
            'is_active' => $this->is_active,
            'total_students' => $this->total_students,
            'total_teachers' => $this->total_teachers,
            'total_classes' => $this->total_classes,
            'total_parents' => $this->total_parents,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
