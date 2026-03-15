<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'items'              => ['required', 'array', 'min:1'],
            'items.*.id'         => ['required', 'integer'],
            'items.*.type'       => ['required', 'string', 'in:course,webinar,product'],
            'items.*.quantity'   => ['required', 'integer', 'min:1', 'max:10'],
            'promo_code'         => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required'         => 'Keranjang tidak boleh kosong.',
            'items.array'            => 'Format item tidak valid.',
            'items.min'              => 'Minimal 1 item untuk checkout.',
            'items.*.id.required'    => 'ID item wajib diisi.',
            'items.*.id.integer'     => 'ID item harus berupa angka.',
            'items.*.type.required'  => 'Tipe item wajib diisi.',
            'items.*.type.in'        => 'Tipe item harus course, webinar, atau product.',
            'items.*.quantity.required' => 'Jumlah item wajib diisi.',
            'items.*.quantity.min'   => 'Jumlah item minimal 1.',
            'items.*.quantity.max'   => 'Jumlah item maksimal 10.',
        ];
    }
}
