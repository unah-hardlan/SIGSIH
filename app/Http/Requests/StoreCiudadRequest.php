<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCiudadRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_ciudad' => 'required|string|max:100',
            'id_departamento_fk' => 'required|exists:tbl_departamento,id_departamento_pk'
        ];
    }
}
