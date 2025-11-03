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
            'nombre_empresa' => 'required|string|max:150',
            'descripcion_empresa' => 'nullable|string|max:500',
            'estado_empresa' => 'required|string|max:20|in:activo,inactivo'
        ];
    }
}
