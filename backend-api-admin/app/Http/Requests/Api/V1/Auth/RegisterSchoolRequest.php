<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterSchoolRequest extends FormRequest
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
            // User Data
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            
            // School Data
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
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.min' => 'Password minimal 8 karakter.',
            
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
