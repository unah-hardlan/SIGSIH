<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTipoProductoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'nombre_tipo_producto' => 'required|string|max:50|unique:tbl_tipo_producto,nombre_tipo_producto',
            'descripcion_tipo_producto' => 'nullable|string|max:255',
        ];
    }
}
