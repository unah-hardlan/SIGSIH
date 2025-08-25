<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoVisitaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_tipo_visita' => 'required|string|max:50|unique:tbl_tipo_visita,nombre_tipo_visita',
            'descripcion_tipo_visita' => 'nullable|string|max:255',
        ];
    }
}
