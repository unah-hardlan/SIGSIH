<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgenciaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_agencia' => 'required|string|max:100|unique:tbl_agencias,nombre_agencia',
            'horario_agencia' => 'required|string|max:50',
            'id_direccion_fk' => 'required|exists:tbl_direccion,id_direccion_pk'
        ];
    }
}
