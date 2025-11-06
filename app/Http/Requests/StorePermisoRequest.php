<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePermisoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_rol_fk' => 'required|integer|exists:tbl_ms_rol,id_rol_pk',
            'id_objeto_fk' => 'required|integer|exists:tbl_objetos,id_objetos_pk',
            'permiso_insercion' => 'required|boolean',
            'permiso_consultar' => 'required|boolean',
            'permiso_ver' => 'sometimes|boolean',
            'permiso_actualizar' => 'required|boolean',
            'permiso_eliminacion' => 'required|boolean',
        ];
    }
}
