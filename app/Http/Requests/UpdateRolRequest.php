<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('role'); 
        return [
            'rol' => 'sometimes|required|string|max:50|unique:tbl_ms_rol,rol,' . $id . ',id_rol_pk',
            'descripcion_rol' => 'sometimes|nullable|string|max:250',
        ];
    }
}
