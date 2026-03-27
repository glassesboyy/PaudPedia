<?php

namespace App\Http\Resources\Api\V1\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url ? asset('storage/' . $this->avatar_url) : null,
            'gender' => $this->gender?->value,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'address' => $this->address,
            'email_verified_at' => $this->email_verified_at?->toISOString(),
            'is_active' => $this->is_active,
            'roles' => $this->whenLoaded('roles', function () {
                return $this->roles->pluck('name');
            }),
            'school_memberships' => $this->whenLoaded('schoolMemberships', function () {
                return $this->schoolMemberships->map(function ($membership) {
                    return [
                        'id' => $membership->id,
                        'school_id' => $membership->school_id,
                        'role_type' => $membership->role_type,
                        'is_active' => $membership->is_active,
                        'school' => [
                            'id' => $membership->school->id,
                            'name' => $membership->school->name,
                            'npsn' => $membership->school->npsn,
                        ]
                    ];
                });
            }),
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
