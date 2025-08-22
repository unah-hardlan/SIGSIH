<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'telefono_contacto' => 'required|string|max:20',
            'correo_contacto' => 'required|email|max:100|unique:tbl_contacto,correo_contacto',
            'id_persona_fk' => 'required|exists:tbl_persona,id_persona_pk'
        ];
    }
}
