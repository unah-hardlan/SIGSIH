<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateNombreEmpresaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('nombres_empresa') ?? $this->route('id');
        return [
            'nombre_empresa' => 'sometimes|required|string|max:150|unique:nombres_empresa,nombre_empresa,' . $id . ',id_nombre_empresa_pk',
            'descripcion_empresa' => 'sometimes|nullable|string|max:500',
            'estado_empresa' => 'sometimes|required|string|max:20|in:activo,inactivo'
        ];
    }
}
