<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ValidatePromoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code'     => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required'     => 'Kode promo wajib diisi.',
            'code.max'          => 'Kode promo maksimal 50 karakter.',
            'subtotal.required' => 'Subtotal wajib diisi.',
            'subtotal.numeric'  => 'Subtotal harus berupa angka.',
            'subtotal.min'      => 'Subtotal tidak boleh negatif.',
        ];
    }
}
