<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoMovimientoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_tipo_movimiento' => 'required|string|max:50|unique:tbl_tipo_movimiento,nombre_tipo_movimiento',
            'descripcion_tipo_movimiento' => 'nullable|string|max:255',
        ];
    }
}
