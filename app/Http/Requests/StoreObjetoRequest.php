<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreObjetoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_objeto' => 'required|string|max:100|unique:tbl_objetos,nombre_objeto',
            'descripcion_objeto' => 'nullable|string|max:255',
            'id_tipo_objetos_fk' => 'required|integer',
        ];
    }
}
