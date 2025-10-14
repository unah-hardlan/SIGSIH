<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $categoriaId = $this->route('categoria')->id_categoria_pk;

        return [
            'nombre_categoria' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('tbl_categorias', 'nombre_categoria')->ignore($categoriaId, 'id_categoria_pk'),
            ],
            'descripcion_categoria' => 'sometimes|nullable|string|max:255',
        ];
    }
}