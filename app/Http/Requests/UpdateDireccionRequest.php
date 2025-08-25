<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDireccionRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'id_ciudad_fk' => 'sometimes|required|exists:tbl_ciudad,id_ciudad_pk'
        ];
    }
}
