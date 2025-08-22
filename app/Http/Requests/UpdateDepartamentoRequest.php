<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartamentoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_departamento' => 'sometimes|required|string|max:100',
            'id_pais_fk' => 'sometimes|required|exists:tbl_pais,id_pais_pk'
        ];
    }
}
