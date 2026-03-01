<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $productId = $this->route('id');

        return [
            'name' => 'required|string|max:125|unique:products,name,'.$productId,
            'slug' => 'nullable|string|max:125|unique:products,slug,'.$productId,
            'status' => 'nullable|integer|in:0,1,2',
            'description' => 'required|string',
            'detail' => 'nullable|json',
            'images' => 'nullable|array',
            'images.*' => 'exists:images,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.unique' => 'Product name already exists.',
            'slug.required' => 'Slug is required.',
            'slug.unique' => 'Slug already exists.',
            'description.required' => 'Product description is required.',

            // If you are uploading files
            'images.*.image' => 'Each file must be an image.',
            'images.*.max' => 'Each image must not exceed 5MB.',

            'categories.*.exists' => 'Selected category is invalid.',
        ];
    }
}
