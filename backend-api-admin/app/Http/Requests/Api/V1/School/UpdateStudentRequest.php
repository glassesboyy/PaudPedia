<?php

namespace App\Http\Requests\Api\V1\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolId = $this->route('id');

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'parent_profile_id' => ['required', 'integer', "exists:parent_profiles,id,school_id,{$schoolId}"],
            'class_id' => ['required', 'integer', "exists:classes,id,school_id,{$schoolId}"],
            'nisn' => ['required', 'numeric', 'digits:10'],
            'birth_date' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'in:male,female'],
            'address' => ['required', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'enrollment_date' => ['required', 'date'],
            'status' => ['required', 'in:active,graduated,transferred'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama siswa wajib diisi.',
            'name.min' => 'Nama siswa minimal 2 karakter.',
            'parent_profile_id.required' => 'Data orang tua wajib dipilih.',
            'parent_profile_id.exists' => 'Data orang tua yang dipilih tidak valid.',
            'class_id.required' => 'Kelas wajib dipilih.',
            'class_id.exists' => 'Kelas yang dipilih tidak valid.',
            'nisn.required' => 'NISN wajib diisi.',
            'nisn.numeric' => 'NISN harus berupa angka.',
            'nisn.digits' => 'NISN harus tepat 10 digit.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'birth_date.before' => 'Tanggal lahir harus sebelum hari ini.',
            'gender.required' => 'Jenis kelamin wajib dipilih.',
            'gender.in' => 'Jenis kelamin harus Laki-laki atau Perempuan.',
            'address.required' => 'Alamat tinggal wajib diisi.',
            'photo.image' => 'File harus berupa gambar.',
            'photo.max' => 'Ukuran foto maksimal 2MB.',
            'enrollment_date.required' => 'Tanggal masuk sekolah wajib diisi.',
            'status.required' => 'Status siswa wajib dipilih.',
        ];
    }
}
