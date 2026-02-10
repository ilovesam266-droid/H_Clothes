<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
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
            'province' => 'required|string|max:125',
            'district' => 'required|string|max:125',
            'ward' => 'nullable|string|max:125',
            'detail' => 'required|string|max:125',
            'is_default' => 'boolean',
        ];
    }
}
