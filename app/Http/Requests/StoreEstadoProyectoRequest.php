<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoProyectoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_estado_proyecto' => 'required|string|max:100|unique:tbl_estado_proyecto,nombre_estado_proyecto',
            'descripcion_estado_proyecto' => 'nullable|string|max:255'
        ];
    }
}
