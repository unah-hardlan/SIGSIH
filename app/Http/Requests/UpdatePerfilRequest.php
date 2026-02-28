<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePerfilRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        
        
        $id = $this->route('perfile')
            ?? $this->route('perfil')
            ?? $this->route('id');

        return [
            'nombre_perfil' => 'sometimes|required|string|max:50|unique:tbl_perfil,nombre_perfil,' . $id . ',id_perfil_pk',
            'descripcion_perfil' => 'sometimes|nullable|string|max:255',
        ];
    }
}
