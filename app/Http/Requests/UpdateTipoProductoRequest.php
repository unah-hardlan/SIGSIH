<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTipoProductoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('tipo_producto') ?? $this->route('id');
        return [
            'nombre_tipo_producto' => 'sometimes|required|string|max:50|unique:tbl_tipo_producto,nombre_tipo_producto,' . $id . ',id_tipo_producto_pk',
            'descripcion_tipo_producto' => 'sometimes|nullable|string|max:255',
        ];
    }
}
