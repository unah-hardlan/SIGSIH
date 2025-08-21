<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('producto') ?? $this->route('id');
        return [
            'nombre_producto' => 'sometimes|required|string|max:100|unique:tbl_producto,nombre_producto,' . $id . ',id_producto_pk',
            'descripcion_producto' => 'sometimes|nullable|string|max:255',
            'precio_unitario' => 'sometimes|required|numeric|min:0',
            'precio_venta' => 'sometimes|required|numeric|min:0',
            'stock_minimo' => 'sometimes|required|integer|min:0',
            'fecha_registro' => 'sometimes|nullable|date',
            'id_tipo_producto_fk' => 'sometimes|required|integer|exists:tbl_tipo_producto,id_tipo_producto_pk',
        ];
    }
}
