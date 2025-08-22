<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('categoria') ?? $this->route('id');
        return [
            'nombre_categoria' => 'sometimes|required|string|max:100|unique:tbl_categorias,nombre_categoria,' . $id . ',id_categoria_pk',
            'descripcion_categoria' => 'sometimes|nullable|string|max:255'
        ];
    }
}
