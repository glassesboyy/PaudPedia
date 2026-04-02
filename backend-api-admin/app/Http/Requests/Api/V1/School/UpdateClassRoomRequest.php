<?php

namespace App\Http\Requests\Api\V1\School;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\ClassRoom;

class UpdateClassRoomRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // The classroom ID comes from the route parameters (e.g., schools/{id}/classes/{classId})
        $classId = $this->route('classId');

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')->ignore($classId)->where(function ($query) {
                    return $query->where('school_id', $this->route('id'))
                        ->where('academic_year', $this->input('academic_year'));
                })
            ],
            'homeroom_teacher_id' => [
                'required',
                'integer',
                // Ensure the teacher exists and belongs to the current school
                Rule::exists('teachers', 'id')->where(function ($query) {
                    $query->where('school_id', $this->route('id'));
                }),
            ],
            'capacity' => [
                'required', 
                'integer', 
                'min:1',
                // We could add a custom rule here to check against current student count if we wanted
                function ($attribute, $value, $fail) use ($classId) {
                    $class = ClassRoom::find($classId);
                    if ($class) {
                        $currentStudents = $class->getCurrentStudentCount();
                        if ($value < $currentStudents) {
                            $fail("Kapasitas tidak boleh lebih kecil dari jumlah siswa saat ini ({$currentStudents}).");
                        }
                    }
                }
            ],
            'level' => ['required', 'string', 'max:50'],
            'academic_year' => ['required', 'string', 'max:20', 'regex:/^\d{4}\/\d{4}$/'],
        ];
    }
    
    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'Nama kelas ini sudah ada pada tahun ajaran yang dipilih.',
            'homeroom_teacher_id.required' => 'Wali kelas wajib dipilih.',
            'homeroom_teacher_id.exists' => 'Wali kelas yang dipilih tidak valid atau bukan dari sekolah ini.',
            'capacity.min' => 'Kapasitas kelas minimal adalah 1 murid.',
            'academic_year.regex' => 'Format tahun ajaran tidak valid (contoh: 2024).',
        ];
    }
}
