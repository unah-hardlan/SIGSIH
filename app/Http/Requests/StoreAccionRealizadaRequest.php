<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAccionRealizadaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_accion' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_accion_realizada', 'nombre_accion')
            ],
            'descripcion_accion' => 'nullable|string|max:255',
        ];
    }
}