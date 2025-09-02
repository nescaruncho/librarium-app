<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => [
                'required',
                'string',
                'max:30',
                Rule::unique(Usuario::class)->ignore($this->user()->idUsuario, 'idUsuario'),
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'max:255',
                Rule::unique(Usuario::class)->ignore($this->user()->idUsuario, 'idUsuario'),
            ],
            'nombre' => 'required|string|max:50',
            'apellido1' => 'required|string|max:50',
            'apellido2' => 'nullable|string|max:50',
            'fecha_nacimiento' => 'nullable|date',
            'genero' => [
                'nullable',
                'string',
                Rule::in(['masculino', 'femenino']),
            ],
            'ciudad' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'privacidad' => 'boolean',
            'notifEmail' => 'boolean',
            'temaDef' => [
                'nullable',
                'string',
                Rule::in(['light', 'dark']),
            ],
        ];
    }
}
