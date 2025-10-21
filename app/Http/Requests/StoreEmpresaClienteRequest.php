<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_comercial' => 'required|string|max:150',
            'razon_social' => 'nullable|string|max:150',
            'rtn' => 'nullable|string|max:30',
            'descripcion_empresa' => 'nullable|string|max:255',
            'horario_atencion' => 'nullable|string|max:50',
            'avatar' => 'nullable|string|max:255',
            'fecha_registro' => 'nullable|date',
            'estado_cliente' => 'nullable|in:activo,inactivo',
        ];
    }
}
