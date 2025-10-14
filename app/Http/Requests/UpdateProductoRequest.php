<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class UpdateProductoRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        $id = $this->route('producto')->id_producto_pk; 

        return [
            'sku' => ['sometimes','required','string','max:50', Rule::unique('tbl_producto', 'sku')->ignore($id, 'id_producto_pk')],
            'nombre_producto' => ['sometimes','required','string','max:100', Rule::unique('tbl_producto', 'nombre_producto')->ignore($id, 'id_producto_pk')],
            'descripcion_producto' => 'sometimes|nullable|string|max:255',
            'precio_unitario' => 'sometimes|required|numeric|min:0',
            'precio_costo' => 'sometimes|nullable|numeric|min:0',
            'precio_venta' => 'sometimes|required|numeric|min:0',
            'stock_minimo' => 'sometimes|required|integer|min:0',
            'fecha_registro' => 'sometimes|nullable|date',
            'id_tipo_producto_fk' => 'sometimes|required|integer|exists:tbl_tipo_producto,id_tipo_producto_pk',
        ];
    }
}