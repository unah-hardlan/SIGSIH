<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOficinaEmpresaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('oficinas_empresa') ?? $this->route('id');
        return [
            'nombre_oficina' => 'sometimes|required|string|max:100|unique:tbl_oficina_empresa,nombre_oficina,' . $id . ',id_oficina_empresa_pk'
        ];
    }
}
