<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServicioRealizadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre_servicio' => 'required|string|max:100|unique:tbl_servicio_realizado,nombre_servicio',
            'descripcion_servicio' => 'nullable|string|max:255',
        ];
    }
}
