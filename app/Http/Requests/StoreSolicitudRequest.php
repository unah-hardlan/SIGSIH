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
            'titulo_solicitud' => 'required|string|max:255',
            'descripcion_solicitud' => 'required|string',
            'fecha_solicitud' => 'required|date',
            'id_contacto_fk' => 'required|exists:tbl_contacto,id_contacto_pk',
            'id_estado_solicitud_fk' => 'required|exists:tbl_estado_solicitud,id_estado_solicitud_pk'
        ];
    }
}
