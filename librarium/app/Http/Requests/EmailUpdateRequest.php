<?php

namespace App\Http\Requests;

use App\Models\Usuario;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmailUpdateRequest extends FormRequest
{
    public function authorize(): bool { return auth()->check(); }

    public function rules(): array
    {
        return [
            'email' => ['required','string','lowercase','email','max:255',
                Rule::unique(Usuario::class)->ignore($this->user()->idUsuario, 'idUsuario')],
        ];
    }
}
