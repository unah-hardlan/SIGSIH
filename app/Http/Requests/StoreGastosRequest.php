<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGastoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_gasto' => 'required|string|max:255',
            'fecha_gasto' => 'required|date',
            'monto_gasto' => 'required|numeric|min:0',
            'descripcion_gasto' => 'nullable|string',
            'id_proyecto_fk' => 'required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'required|exists:tbl_categorias,id_categoria_pk'
        ];
    }
}
