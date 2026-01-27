<?php

namespace App\Http\Requests;

use App\Enums\UserRole;
use App\Enums\UserSex;
use App\Enums\UserStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
            'first_name' => 'required|string|max:125|regex:/^[\p{L}\s]+$/u',
            'last_name' => 'required|string|max:255|regex:/^[\p{L}\s]+$/u',
            'user_name' => 'required|string|max:125|regex:/^[a-zA-Z0-9_]+$/|unique:users,user_name',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'confirmed',
                Password::min(8)->letters()->mixedCase()->numbers()->symbols(),
            ],
            'birthday' => 'nullable|date|before:today|after:1900-01-01',
            'sex' => 'required|integer|' . Rule::in(array_column(UserSex::cases(), 'value')),
            'role' => 'required|integer|' . Rule::in(array_column(UserRole::cases(), 'value')),
            'status' => 'required|integer|' . Rule::in(array_column(UserStatus::cases(), 'value')),
            'email_verified' => 'nullable|boolean',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=2000,max_height=2000',
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'first_name.string' => 'First name must be a string.',
            'first_name.max' => 'First name may not be greater than 125 characters.',
            'first_name.regex' => 'First name may only contain letters and spaces.',

            'last_name.required' => 'Last name is required.',
            'last_name.string' => 'Last name must be a string.',
            'last_name.max' => 'Last name may not be greater than 255 characters.',
            'last_name.regex' => 'Last name may only contain letters and spaces.',

            'user_name.required' => 'Username is required.',
            'user_name.string' => 'Username must be a string.',
            'user_name.max' => 'Username may not be greater than 125 characters.',
            'user_name.regex' => 'Username may only contain letters, numbers, and underscores.',
            'user_name.unique' => 'This username is already taken.',

            'email.required' => 'Email is required.',
            'email.string' => 'Email must be a string.',
            'email.email' => 'Email must be a valid email address.',
            'email.max' => 'Email may not be greater than 255 characters.',
            'email.unique' => 'This email is already registered.',

            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a string.',
            'password.confirmed' => 'Password confirmation does not match.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.letters' => 'Password must contain at least one letter.',
            'password.mixedCase' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one symbol.',
            'password.uncompromised' => 'This password has appeared in a data breach. Please choose another.',

            'birthday.date' => 'Birthday must be a valid date.',
            'birthday.before' => 'Birthday must be before today.',
            'birthday.after' => 'Birthday must be after January 1, 1900.',

            'sex.required' => 'Sex is required.',
            'sex.integer' => 'Sex must be an integer.',
            'sex.in' => 'Sex must be one of: Other, Male, Female.',

            'role.required' => 'Role is required.',
            'role.integer' => 'Role must be an integer.',
            'role.in' => 'Role must be either Admin or User.',

            'status.required' => 'Status is required.',
            'status.integer' => 'Status must be an integer.',
            'status.in' => 'Status must be either Active or Inactive.',

            'email_verified.boolean' => 'Email verified must be true or false.',

            'avatar.image' => 'Avatar must be an image.',
            'avatar.mimes' => 'Avatar must be a file of type: jpeg, jpg, png, gif, webp.',
            'avatar.max' => 'Avatar may not be larger than 2MB.',
            'avatar.dimensions' => 'Avatar dimensions must be between 100x100 and 2000x2000 pixels.',
        ];
    }
}
