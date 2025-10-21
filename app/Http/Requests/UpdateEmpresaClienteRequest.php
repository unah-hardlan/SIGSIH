<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmpresaClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_comercial' => 'sometimes|required|string|max:150',
            'razon_social' => 'sometimes|nullable|string|max:150',
            'rtn' => 'sometimes|nullable|string|max:30',
            'descripcion_empresa' => 'sometimes|nullable|string|max:255',
            'horario_atencion' => 'sometimes|nullable|string|max:50',
            'avatar' => 'sometimes|nullable|string|max:255',
            'fecha_registro' => 'sometimes|date',
            'estado_cliente' => 'sometimes|in:activo,inactivo',
        ];
    }
}
