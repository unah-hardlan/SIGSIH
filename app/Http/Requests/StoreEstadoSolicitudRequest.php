<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoSolicitudRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_estado_solicitud' => 'required|string|max:100|unique:tbl_estado_solicitud,nombre_estado_solicitud',
            'descripcion_estado_solicitud' => 'nullable|string|max:255'
        ];
    }
}
