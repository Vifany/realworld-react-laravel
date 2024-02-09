<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
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
            'user.username' => 'required|max:64|unique:profiles,username',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'password.required' => 'The password field is required.',
            'username.unique' => 'This username has already been taken',
            'email.unique' => 'This email has already been used',
        ];
    }
}
