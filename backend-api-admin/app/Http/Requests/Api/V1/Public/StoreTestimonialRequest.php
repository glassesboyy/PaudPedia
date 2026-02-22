<?php

namespace App\Http\Requests\Api\V1\Public;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestimonialRequest extends FormRequest
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
            'name' => ['required_without:user_id', 'string', 'max:100'],
            'title' => ['nullable', 'string', 'max:100'],
            'content' => ['required', 'string', 'min:10', 'max:1000'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
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
            'name.required_without' => 'Nama wajib diisi jika belum login.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'title.string' => 'Jabatan/profesi harus berupa teks.',
            'title.max' => 'Jabatan/profesi maksimal 100 karakter.',
            'content.required' => 'Isi testimonial wajib diisi.',
            'content.string' => 'Isi testimonial harus berupa teks.',
            'content.min' => 'Isi testimonial minimal 10 karakter.',
            'content.max' => 'Isi testimonial maksimal 1000 karakter.',
            'rating.required' => 'Rating wajib diisi.',
            'rating.integer' => 'Rating harus berupa angka.',
            'rating.min' => 'Rating minimal 1.',
            'rating.max' => 'Rating maksimal 5.',
        ];
    }
}
