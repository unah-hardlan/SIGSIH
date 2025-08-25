<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIngresosRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_ingreso' => 'sometimes|required|string|max:255',
            'fecha_ingreso' => 'sometimes|required|date',
            'monto_ingreso' => 'sometimes|required|numeric|min:0',
            'descripcion_ingreso' => 'sometimes|nullable|string',
            'id_proyecto_fk' => 'sometimes|required|exists:tbl_proyectos,id_proyecto_pk',
            'id_categoria_fk' => 'sometimes|required|exists:tbl_categorias,id_categoria_pk'
        ];
    }
}
