<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRolRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'rol' => 'required|string|max:50|unique:tbl_ms_rol,rol',
            'descripcion_rol' => 'nullable|string|max:250',
        ];
    }
}
