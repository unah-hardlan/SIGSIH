<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCalendarioRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'fecha' => 'required|date',
            'descripcion_calendario' => 'required|string|max:255',
            'observaciones_calendario' => 'nullable|string|max:255',
            'id_estado_calendario_fk' => 'required|integer|exists:tbl_estado_calendario,id_estado_calendario_pk',
            'id_agencias_fk' => 'required|integer|exists:tbl_agencias,id_agencias_pk',
            'id_orden_servicio_fk' => 'required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_tipo_mantenimiento_fk' => 'required|integer|exists:tbl_tipo_mantenimiento,id_tipo_mantenimiento_pk',
            'id_cliente_fk' => 'required|integer|exists:tbl_persona,id_persona_pk'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fecha.required' => 'La fecha es obligatoria',
            'fecha.date' => 'La fecha debe ser una fecha válida',
            'descripcion_calendario.required' => 'La descripción es obligatoria',
            'descripcion_calendario.max' => 'La descripción no puede exceder 255 caracteres',
            'observaciones_calendario.max' => 'Las observaciones no pueden exceder 255 caracteres',
            'id_estado_calendario_fk.required' => 'El estado del calendario es obligatorio',
            'id_estado_calendario_fk.exists' => 'El estado del calendario especificado no existe',
            'id_agencias_fk.required' => 'La agencia es obligatoria',
            'id_agencias_fk.exists' => 'La agencia especificada no existe',
            'id_orden_servicio_fk.required' => 'La orden de servicio es obligatoria',
            'id_orden_servicio_fk.exists' => 'La orden de servicio especificada no existe',
            'id_tipo_mantenimiento_fk.required' => 'El tipo de mantenimiento es obligatorio',
            'id_tipo_mantenimiento_fk.exists' => 'El tipo de mantenimiento especificado no existe',
            'id_cliente_fk.required' => 'El cliente es obligatorio',
            'id_cliente_fk.exists' => 'El cliente especificado no existe'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
