<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParametroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'parametro' => 'required|string|max:100|unique:tbl_parametros,parametro',
            
            'valor' => 'required|string|max:255',
            
            'id_usuario_fk' => 'nullable|integer|exists:tbl_ms_usuario,id_usuario_pk',
        ];

        
        if (strtoupper($this->input('parametro') ?? '') === 'ADMIN.CORREO') {
            
            
            
            $rules['valor'] = [
                'required',
                'string',
                'max:255',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            ];
        }

        return $rules;
    }
}
