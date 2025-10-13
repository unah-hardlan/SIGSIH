<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:50|unique:tbl_producto,sku',
            'nombre_producto' => 'required|string|max:100|unique:tbl_producto,nombre_producto',
            'descripcion_producto' => 'nullable|string|max:255',
            'precio_unitario' => 'required|numeric|min:0',
            'precio_costo' => 'nullable|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'fecha_registro' => 'nullable|date',
            'id_tipo_producto_fk' => 'required|integer|exists:tbl_tipo_producto,id_tipo_producto_pk',
        ];
    }
}
