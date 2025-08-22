<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEstadoSolicitudRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('estados_solicitud') ?? $this->route('id');
        return [
            'nombre_estado_solicitud' => 'sometimes|required|string|max:100|unique:tbl_estado_solicitud,nombre_estado_solicitud,' . $id . ',id_estado_solicitud_pk',
            'descripcion_estado_solicitud' => 'sometimes|nullable|string|max:255'
        ];
    }
}
