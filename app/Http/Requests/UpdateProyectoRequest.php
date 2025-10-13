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
            'nombre_proyecto' => 'sometimes|required|string|max:100',
            'descripcion_proyecto' => 'sometimes|nullable|string|max:500',
            'fecha_inicio_proyecto' => 'sometimes|required|date',
            'fecha_estimada_fin_proyecto' => 'sometimes|nullable|date',
            'fecha_finalizacion_proyecto' => 'sometimes|nullable|date',
            'id_orden_servicio_fk' => 'sometimes|required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_estado_proyecto_fk' => 'sometimes|required|integer|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ];
    }
}
