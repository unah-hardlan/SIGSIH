<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrdenServicioRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'fecha_orden' => 'required|date',
            'descripcion_trabajo' => 'required|string',
            'monto_orden' => 'required|numeric|min:0',
            'id_persona_cliente_fk' => 'required|exists:tbl_persona,id_persona_pk',
            'id_persona_tecnico_fk' => 'required|exists:tbl_persona,id_persona_pk'
        ];
    }
}
