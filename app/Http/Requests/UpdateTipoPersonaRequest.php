<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoPersonaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('tipo_persona');
        return [
            'nombre_tipo_persona' => 'sometimes|required|string|max:50|unique:tbl_tipo_persona,nombre_tipo_persona,' . $id . ',id_tipo_persona_pk',
            'descripcion' => 'sometimes|nullable|string|max:255',
        ];
    }
}
