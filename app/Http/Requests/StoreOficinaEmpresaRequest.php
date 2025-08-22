<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOficinaEmpresaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_oficina' => 'required|string|max:100|unique:tbl_oficina_empresa,nombre_oficina'
        ];
    }
}
