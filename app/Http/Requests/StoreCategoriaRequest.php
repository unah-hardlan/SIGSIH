<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_categoria' => 'required|string|max:100|unique:tbl_categorias,nombre_categoria',
            'descripcion_categoria' => 'nullable|string|max:255'
        ];
    }
}
