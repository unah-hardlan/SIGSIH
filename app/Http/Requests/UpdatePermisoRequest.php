<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePermisoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_rol_fk' => 'sometimes|required|integer|exists:tbl_ms_rol,id_rol_pk',
            'id_objeto_fk' => 'sometimes|required|integer|exists:tbl_objetos,id_objetos_pk',
            'permiso_insercion' => 'sometimes|required|boolean',
            'permiso_consultar' => 'sometimes|required|boolean',
            'permiso_ver' => 'sometimes|boolean',
            'permiso_actualizar' => 'sometimes|required|boolean',
            'permiso_eliminacion' => 'sometimes|required|boolean',
        ];
    }
}
