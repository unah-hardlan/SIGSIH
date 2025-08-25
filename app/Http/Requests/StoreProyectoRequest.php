<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProyectoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_proyecto' => 'required|string|max:255',
            'descripcion_proyecto' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'presupuesto' => 'required|numeric|min:0',
            'id_solicitud_fk' => 'required|exists:tbl_solicitud,id_solicitud_pk',
            'id_estado_proyecto_fk' => 'required|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ];
    }
}
