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
            'nombre' => 'sometimes|required|string|max:100|unique:tbl_estado_solicitud,nombre,' . $id . ',id_estado_solicitud_pk',
            'descripcion' => 'sometimes|nullable|string|max:255',
            'codigo' => 'sometimes|nullable|string|max:50',
            'es_final' => 'sometimes|nullable|boolean',
            'orden' => 'sometimes|nullable|integer|min:0'
        ];
    }
}
