<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDireccionRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'id_ciudad_fk' => 'required|exists:tbl_ciudad,id_ciudad_pk'
        ];
    }
}
