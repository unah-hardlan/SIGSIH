<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContactoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('contacto') ?? $this->route('id');
        return [
            'telefono_contacto' => 'sometimes|required|string|max:20',
            'correo_contacto' => 'sometimes|required|email|max:100|unique:tbl_contacto,correo_contacto,' . $id . ',id_contacto_pk',
            'id_persona_fk' => 'sometimes|required|exists:tbl_persona,id_persona_pk'
        ];
    }
}
