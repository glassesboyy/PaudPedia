<?php

namespace App\Http\Resources\Api\V1\School;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassRoomResource extends JsonResource
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
            'school_id' => $this->school_id,
            'homeroom_teacher_id' => $this->homeroom_teacher_id,
            'homeroom_teacher_name' => $this->homeroom_teacher_name, // from getHomeroomTeacherNameAttribute
            'name' => $this->name,
            'level' => $this->level,
            'capacity' => $this->capacity,
            'current_students' => $this->getCurrentStudentCount(), // live calc
            'academic_year' => $this->academic_year,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
