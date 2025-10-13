<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSolicitudRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('solicitude') ?? $this->route('solicitud') ?? $this->route('id');

        return [
            'id_cliente_fk' => 'sometimes|required|integer|exists:tbl_cliente,id_cliente_pk',
            'numero_solicitud_acf' => 'sometimes|required|integer|unique:tbl_solicitud,numero_solicitud_acf,' . $id . ',id_solicitud_pk',
            'numero_solicitud_cliente' => 'sometimes|required|integer',
            'descripcion_problema' => 'sometimes|required|string|max:500',
            'id_estado_solicitud_fk' => 'sometimes|required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => 'sometimes|required|integer|exists:tbl_contacto,id_contacto_pk',
        ];
    }
}
