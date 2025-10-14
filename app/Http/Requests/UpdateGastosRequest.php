<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGastoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_gasto' => 'sometimes|required|string|max:255',
            'fecha_gasto' => 'sometimes|required|date',
            'monto_gasto' => 'sometimes|required|numeric|min:0',
            'descripcion_gasto' => 'sometimes|nullable|string',
            'id_proyecto_fk' => 'sometimes|required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'sometimes|required|exists:tbl_categorias,id_categoria_pk'
        ];
    }
}
