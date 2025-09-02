<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'username' => 'required|string|max:30|unique:usuario,username',
            'email' => 'required|email|max:255|unique:usuario,email',
            'password' => 'required|string|min:8|confirmed',
            'nombre' => 'required|string|max:50',
            'apellidos' => 'required|string|max:100'
        ];
    }
}
