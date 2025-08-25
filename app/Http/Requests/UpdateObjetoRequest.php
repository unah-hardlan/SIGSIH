<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateObjetoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? $this->route('objeto') ?? $this->route('objetos') ?? $this->route('tbl_objetos');

        return [
            'nombre_objeto' => 'sometimes|required|string|max:100|unique:tbl_objetos,nombre_objeto,' . $id . ',id_objetos_pk',
            'descripcion_objeto' => 'sometimes|nullable|string|max:255',
            'id_tipo_objetos_fk' => 'sometimes|required|integer',
        ];
    }
}
