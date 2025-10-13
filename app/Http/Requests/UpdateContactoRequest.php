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
        return [
            'tipo_contacto' => 'sometimes|required|string|in:email,tel,whatsapp',
            'valor_contacto' => 'sometimes|required|string|max:255',
            'id_cliente_fk' => 'sometimes|required|integer|exists:tbl_cliente,id_cliente_pk',
        ];
    }
}
