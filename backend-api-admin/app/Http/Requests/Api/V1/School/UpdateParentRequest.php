<?php

namespace App\Http\Requests\Api\V1\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $schoolId = $this->route('id');
        $parentId = $this->route('parentId');

        return [
            'email' => [
                'required',
                'email',
                'max:255',
                "unique:parent_profiles,email,{$parentId},id,school_id,{$schoolId}",
            ],
            'father_name' => ['required', 'string', 'max:255'],
            'mother_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'numeric', 'min_digits:10', 'max_digits:15'],
            'father_occupation' => ['required', 'string', 'max:255'],
            'mother_occupation' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email orang tua wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar di sekolah ini.',
            'father_name.required' => 'Nama ayah wajib diisi.',
            'mother_name.required' => 'Nama ibu wajib diisi.',
            'phone.required' => 'Nomor telepon wajib diisi.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.min_digits' => 'Nomor telepon minimal 10 digit.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 digit.',
            'father_occupation.required' => 'Pekerjaan ayah wajib diisi.',
            'mother_occupation.required' => 'Pekerjaan ibu wajib diisi.',
            'address.required' => 'Alamat tinggal wajib diisi.',
        ];
    }
}
