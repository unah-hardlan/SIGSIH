<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNombreEmpresaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_empresa' => 'required|string|max:100|unique:tbl_nombre_empresa,nombre_empresa',
            'descripcion_empresa' => 'nullable|string|max:255',
            'estado_empresa' => 'required|string|max:20|in:activo,inactivo'
        ];
    }
}
