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
            'usuario'    => ['required','string','max:50','regex:/^\S+$/'],
            'contrasena' => ['required','string','max:100','regex:/^\S+$/'],
        ];
    }
}
