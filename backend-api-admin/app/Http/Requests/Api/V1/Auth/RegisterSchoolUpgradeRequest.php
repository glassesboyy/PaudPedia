<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterSchoolUpgradeRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'school_name' => ['required', 'string', 'min:3', 'max:255'],
            'school_npsn' => ['required', 'string', 'digits:8', 'unique:schools,npsn'],
            'school_address' => ['required', 'string', 'min:10'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'school_name.min' => 'Nama sekolah minimal 3 karakter.',
            'school_npsn.required' => 'NPSN wajib diisi.',
            'school_npsn.digits' => 'NPSN harus 8 digit angka.',
            'school_npsn.unique' => 'NPSN sudah terdaftar.',
            'school_address.required' => 'Alamat sekolah wajib diisi.',
            'school_address.min' => 'Alamat minimal 10 karakter.',
        ];
    }
}
