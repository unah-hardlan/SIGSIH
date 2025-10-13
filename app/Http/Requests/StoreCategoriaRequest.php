<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_categoria' => [
                'required',
                'string',
                'max:100',
                Rule::unique('tbl_categorias', 'nombre_categoria')
            ],
            'descripcion_categoria' => 'nullable|string|max:255',
        ];
    }
}