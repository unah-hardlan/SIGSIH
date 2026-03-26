<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario'    => ['required', 'string', 'max:50', 'regex:/^[A-Za-z0-9]+$/'],
            'contrasena' => ['required', 'string', 'max:100', 'regex:/^(?!.*\s)[\x21-\x7E\xA1-\xFF]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'usuario.regex' => 'El usuario solo puede contener letras y números.',
            'contrasena.regex' => 'La contraseña no puede contener espacios ni caracteres de alfabetos no latinos (por ejemplo: 名前).',
        ];
    }
}
