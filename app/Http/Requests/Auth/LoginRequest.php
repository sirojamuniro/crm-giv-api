<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'username' => 'required_without:email|string|max:255|exists:users,username,deleted_at,NULL',
            'email' => 'required_without:username|string|email|max:255|exists:users,email,deleted_at,NULL',
            'password' => 'required|string',
        ];
    }
}
