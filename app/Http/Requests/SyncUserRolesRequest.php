<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SyncUserRolesRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'roles' => 'required|array|min:0',
            'roles.*' => 'integer|exists:tbl_ms_rol,id_rol_pk',
        ];
    }
}
