<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoSolicitudRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $estadoSolicitudId = $this->route('estadoSolicitud')->id_estado_solicitud_pk;

        return [
            'nombre' => [
                'sometimes', 'required', 'string', 'max:100',
                Rule::unique('tbl_estado_solicitud', 'nombre')->ignore($estadoSolicitudId, 'id_estado_solicitud_pk')
            ],
            'codigo' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique('tbl_estado_solicitud', 'codigo')->ignore($estadoSolicitudId, 'id_estado_solicitud_pk')
            ],
            'descripcion' => 'sometimes|nullable|string|max:255',
            'es_final' => 'sometimes|required|boolean',
            'orden' => 'sometimes|required|integer',
        ];
    }
}