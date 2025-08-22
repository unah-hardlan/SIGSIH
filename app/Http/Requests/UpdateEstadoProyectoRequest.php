<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoProyectoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('estados_proyecto') ?? $this->route('id');
        return [
            'nombre_estado_proyecto' => 'sometimes|required|string|max:100|unique:tbl_estado_proyecto,nombre_estado_proyecto,' . $id . ',id_estado_proyecto_pk',
            'descripcion_estado_proyecto' => 'sometimes|nullable|string|max:255'
        ];
    }
}
