<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDetalleOrdenProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_orden_servicio_fk' => 'sometimes|required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_producto_fk' => 'sometimes|required|integer|exists:tbl_producto,id_producto_pk',
            'cantidad' => 'sometimes|required|numeric|min:1'
        ];
    }

    public function messages(): array
    {
        return [
            'id_orden_servicio_fk.required' => 'La orden de servicio es obligatoria',
            'id_orden_servicio_fk.exists' => 'La orden de servicio no existe',
            'id_producto_fk.required' => 'El producto es obligatorio',
            'id_producto_fk.exists' => 'El producto no existe',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.numeric' => 'La cantidad debe ser numérica',
            'cantidad.min' => 'La cantidad debe ser al menos 1'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
