<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreDetalleFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_factura_fk' => 'required|integer|exists:tbl_factura,id_factura_pk',
            'id_servicio_fk' => 'required|integer|exists:tbl_servicio,id_servicio_pk',
            'descripcion' => 'nullable|string|max:500',
            'precio_unitario' => 'required|numeric|min:0',
            'cantidad' => 'required|numeric|min:0.01',
            'impuesto' => 'nullable|numeric|min:0',
            'descuento' => 'nullable|numeric|min:0',
            'fecha_servicio' => 'required|date',
            'horas' => 'nullable|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'id_factura_fk.required' => 'La factura es obligatoria',
            'id_factura_fk.exists' => 'La factura no existe',
            'id_servicio_fk.required' => 'El servicio es obligatorio',
            'id_servicio_fk.exists' => 'El servicio no existe',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres',
            'precio_unitario.required' => 'El precio unitario es obligatorio',
            'precio_unitario.numeric' => 'El precio unitario debe ser numérico',
            'precio_unitario.min' => 'El precio unitario no puede ser negativo',
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.numeric' => 'La cantidad debe ser numérica',
            'cantidad.min' => 'La cantidad debe ser mayor a 0',
            'impuesto.numeric' => 'El impuesto debe ser numérico',
            'impuesto.min' => 'El impuesto no puede ser negativo',
            'descuento.numeric' => 'El descuento debe ser numérico',
            'descuento.min' => 'El descuento no puede ser negativo',
            'fecha_servicio.required' => 'La fecha de servicio es obligatoria',
            'fecha_servicio.date' => 'La fecha de servicio debe ser válida',
            'horas.numeric' => 'Las horas deben ser numéricas',
            'horas.min' => 'Las horas no pueden ser negativas'
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
