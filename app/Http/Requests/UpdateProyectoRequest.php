<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProyectoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_proyecto' => 'sometimes|required|string|max:255',
            'descripcion_proyecto' => 'sometimes|required|string',
            'fecha_inicio' => 'sometimes|required|date',
            'fecha_fin' => 'sometimes|nullable|date|after_or_equal:fecha_inicio',
            'presupuesto' => 'sometimes|required|numeric|min:0',
            'id_solicitud_fk' => 'sometimes|required|exists:tbl_solicitud,id_solicitud_pk',
            'id_estado_proyecto_fk' => 'sometimes|required|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ];
    }
}
