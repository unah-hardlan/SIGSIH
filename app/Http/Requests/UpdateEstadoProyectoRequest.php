<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEstadoProyectoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $estadoProyectoId = $this->route('estadoProyecto')->id_estado_proyecto_pk;

        return [
            'nombre' => [
                'sometimes', 'required', 'string', 'max:100',
                Rule::unique('tbl_estado_proyecto', 'nombre')->ignore($estadoProyectoId, 'id_estado_proyecto_pk')
            ],
            'codigo' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique('tbl_estado_proyecto', 'codigo')->ignore($estadoProyectoId, 'id_estado_proyecto_pk')
            ],
            'descripcion' => 'sometimes|nullable|string|max:255',
            'es_final' => 'sometimes|required|boolean',
            'orden' => 'sometimes|required|integer',
        ];
    }
}