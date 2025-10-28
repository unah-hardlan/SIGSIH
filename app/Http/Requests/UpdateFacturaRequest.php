<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'numero' => 'sometimes|required|string|max:20|unique:tbl_factura,numero,' . $this->route('factura')->id_factura_pk . ',id_factura_pk',
            'fecha' => 'sometimes|required|date',
            'oc' => 'sometimes|nullable|string|max:20',
            'subtotal' => 'sometimes|required|numeric|min:0',
            'impuesto' => 'sometimes|required|numeric|min:0',
            'total' => 'sometimes|required|numeric|min:0',
            'total_letras' => 'sometimes|nullable|string|max:500',
            'id_estado_factura_fk' => 'sometimes|required|integer|exists:tbl_estado_factura,id_estado_factura_pk',
            'id_cai_fk' => 'sometimes|required|integer|exists:tbl_cai,id_cai_pk',
            'id_cliente_fk' => 'sometimes|required|integer|exists:tbl_cliente,id_cliente_pk'
        ];
    }

    public function messages(): array
    {
        return [
            'numero.required' => 'El número de factura es obligatorio',
            'numero.unique' => 'Ya existe una factura con ese número',
            'numero.max' => 'El número no puede exceder 20 caracteres',
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'La fecha debe ser válida',
            'oc.max' => 'La OC no puede exceder 20 caracteres',
            'subtotal.required' => 'El subtotal es obligatorio',
            'subtotal.numeric' => 'El subtotal debe ser numérico',
            'impuesto.required' => 'El impuesto es obligatorio',
            'impuesto.numeric' => 'El impuesto debe ser numérico',
            'total.required' => 'El total es obligatorio',
            'total.numeric' => 'El total debe ser numérico',
            'total_letras.max' => 'El total en letras no puede exceder 500 caracteres',
            'id_estado_factura_fk.required' => 'El estado de factura es obligatorio',
            'id_estado_factura_fk.exists' => 'El estado de factura no existe',
            'id_cai_fk.required' => 'El CAI es obligatorio',
            'id_cai_fk.exists' => 'El CAI no existe',
            'id_cliente_fk.required' => 'El cliente es obligatorio',
            'id_cliente_fk.exists' => 'El cliente no existe'
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
