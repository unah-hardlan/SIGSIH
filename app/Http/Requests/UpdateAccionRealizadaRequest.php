<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccionRealizadaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $accionId = $this->route('acciones_realizada')->id_accion_realizada_pk;

        return [
            'nombre_accion' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_accion_realizada', 'nombre_accion')->ignore($accionId, 'id_accion_realizada_pk')
            ],
            'descripcion_accion' => 'sometimes|nullable|string|max:255',
        ];
    }
}