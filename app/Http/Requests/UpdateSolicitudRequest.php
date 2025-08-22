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
        return [
            'titulo_solicitud' => 'sometimes|required|string|max:255',
            'descripcion_solicitud' => 'sometimes|required|string',
            'fecha_solicitud' => 'sometimes|required|date',
            'id_contacto_fk' => 'sometimes|required|exists:tbl_contacto,id_contacto_pk',
            'id_estado_solicitud_fk' => 'sometimes|required|exists:tbl_estado_solicitud,id_estado_solicitud_pk'
        ];
    }
}
