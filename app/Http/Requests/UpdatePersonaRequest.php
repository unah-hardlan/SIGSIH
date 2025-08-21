<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePersonaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('persona') ?? $this->route('id');
        return [
            'primer_nombre' => 'sometimes|required|string|max:50',
            'segundo_nombre' => 'sometimes|nullable|string|max:50',
            'primer_apellido' => 'sometimes|required|string|max:50',
            'segundo_apellido' => 'sometimes|nullable|string|max:50',
            'dni' => 'sometimes|required|string|max:20|unique:tbl_persona,dni,' . $id . ',id_persona_pk',
            'cargo' => 'sometimes|nullable|string|max:50',
            'id_tipo_persona_fk' => 'sometimes|required|integer|exists:tbl_tipo_persona,id_tipo_persona_pk',
            'id_genero_fk' => 'sometimes|required|integer|exists:tbl_genero,id_genero_pk',
            'id_perfil_fk' => 'sometimes|required|integer|exists:tbl_perfil,id_perfil_pk',
            'id_usuario_fk' => 'sometimes|nullable|integer|exists:tbl_ms_usuario,id_usuario_pk',
        ];
    }
}
