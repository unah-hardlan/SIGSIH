<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateDetalleFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_factura_fk' => 'sometimes|required|integer|exists:tbl_factura,id_factura_pk',
            'id_servicio_fk' => 'sometimes|required|integer|exists:tbl_servicio,id_servicio_pk',
            'fecha_servicio' => 'sometimes|required|date',
            'horas' => 'sometimes|required|numeric|min:0',
            'descuento' => 'sometimes|nullable|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'id_factura_fk.required' => 'La factura es obligatoria',
            'id_factura_fk.exists' => 'La factura no existe',
            'id_servicio_fk.required' => 'El servicio es obligatorio',
            'id_servicio_fk.exists' => 'El servicio no existe',
            'fecha_servicio.required' => 'La fecha de servicio es obligatoria',
            'fecha_servicio.date' => 'La fecha de servicio debe ser válida',
            'horas.required' => 'Las horas son obligatorias',
            'horas.numeric' => 'Las horas deben ser numéricas',
            'descuento.numeric' => 'El descuento debe ser numérico',
            'descuento.min' => 'El descuento no puede ser negativo'
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
