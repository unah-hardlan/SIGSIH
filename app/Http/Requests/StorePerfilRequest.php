<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePerfilRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_perfil' => 'required|string|max:50|unique:tbl_perfil,nombre_perfil',
            'descripcion_perfil' => 'nullable|string|max:255',
        ];
    }
}
