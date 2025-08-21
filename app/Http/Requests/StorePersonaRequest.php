<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersonaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'primer_nombre' => 'required|string|max:50',
            'segundo_nombre' => 'nullable|string|max:50',
            'primer_apellido' => 'required|string|max:50',
            'segundo_apellido' => 'nullable|string|max:50',
            'dni' => 'required|string|max:20|unique:tbl_persona,dni',
            'cargo' => 'nullable|string|max:50',
            'id_tipo_persona_fk' => 'required|integer|exists:tbl_tipo_persona,id_tipo_persona_pk',
            'id_genero_fk' => 'required|integer|exists:tbl_genero,id_genero_pk',
            'id_perfil_fk' => 'required|integer|exists:tbl_perfil,id_perfil_pk',
            'id_usuario_fk' => 'nullable|integer|exists:tbl_ms_usuario,id_usuario_pk',
        ];
    }
}
