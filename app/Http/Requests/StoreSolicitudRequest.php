<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSolicitudRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'id_cliente_fk' => 'required|integer|exists:tbl_cliente,id_cliente_pk',
            'numero_solicitud_acf' => 'required|integer|unique:tbl_solicitud,numero_solicitud_acf',
            'numero_solicitud_cliente' => 'required|integer',
            'descripcion_problema' => 'required|string|max:500',
            'id_estado_solicitud_fk' => 'required|integer|exists:tbl_estado_solicitud,id_estado_solicitud_pk',
            'id_contacto_fk' => 'required|integer|exists:tbl_contacto,id_contacto_pk',
        ];
    }
}
