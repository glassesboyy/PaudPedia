<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'id'       => ['required', 'integer'],
            'type'     => ['required', 'string', 'in:course,webinar,product'],
            'quantity' => ['required', 'integer', 'min:0', 'max:10'],
        ];
    }
}
