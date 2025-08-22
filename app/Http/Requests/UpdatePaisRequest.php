<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaisRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('paise') ?? $this->route('id');
        return [
            'nombre_pais' => 'sometimes|required|string|max:100|unique:tbl_pais,nombre_pais,' . $id . ',id_pais_pk'
        ];
    }
}
