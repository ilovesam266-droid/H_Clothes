<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VariantRequest extends FormRequest
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
        $variantId = $this->route('idV');

        $variantRules = [
            'size' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:30',
            'sku' => ['required', 'string', 'max:100', Rule::unique('variants', 'sku')->ignore($variantId)],
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ];

        return $variantRules;
    }
}
