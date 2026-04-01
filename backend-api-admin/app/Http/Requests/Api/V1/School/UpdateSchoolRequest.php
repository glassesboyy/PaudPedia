<?php

namespace App\Http\Requests\Api\V1\School;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSchoolRequest extends FormRequest
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
        $schoolId = $this->route('id');

        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'npsn' => ['required', 'numeric', 'digits:8', 'unique:schools,npsn,' . $schoolId],
            'address' => ['required', 'string', 'min:10'],
            'phone' => ['nullable', 'numeric', 'min_digits:10', 'max_digits:15'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama sekolah wajib diisi.',
            'name.min' => 'Nama sekolah minimal 3 karakter.',
            'npsn.required' => 'NPSN wajib diisi.',
            'npsn.numeric' => 'NPSN harus berupa angka.',
            'npsn.digits' => 'NPSN harus tepat 8 digit.',
            'npsn.unique' => 'NPSN sudah terdaftar.',
            'address.required' => 'Alamat sekolah wajib diisi.',
            'address.min' => 'Alamat minimal 10 karakter.',
            'phone.numeric' => 'Nomor telepon harus berupa angka.',
            'phone.min_digits' => 'Nomor telepon minimal 10 digit.',
            'phone.max_digits' => 'Nomor telepon maksimal 15 digit.',
            'email.email' => 'Format email tidak valid.',
            'logo.image' => 'File harus berupa gambar.',
            'logo.mimes' => 'Format gambar harus jpeg, png, jpg, atau svg.',
            'logo.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
