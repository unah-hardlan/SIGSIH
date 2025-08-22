<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrdenServicioRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'fecha_orden' => 'sometimes|required|date',
            'descripcion_trabajo' => 'sometimes|required|string',
            'monto_orden' => 'sometimes|required|numeric|min:0',
            'id_persona_cliente_fk' => 'sometimes|required|exists:tbl_persona,id_persona_pk',
            'id_persona_tecnico_fk' => 'sometimes|required|exists:tbl_persona,id_persona_pk'
        ];
    }
}
