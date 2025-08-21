<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoPersonaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_tipo_persona' => 'required|string|max:50|unique:tbl_tipo_persona,nombre_tipo_persona',
            'descripcion' => 'nullable|string|max:255',
        ];
    }
}
