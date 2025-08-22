<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTicketRequest extends FormRequest
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
            'fecha_creacion' => 'required|date',
            'descripcion_ticket' => 'required|string|max:500',
            'id_estado_ticket_fk' => 'required|integer|exists:tbl_estado_ticket,id_estado_ticket_pk',
            'id_tecnico_fk' => 'required|integer|exists:tbl_persona,id_persona_pk',
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
            'fecha_creacion.required' => 'La fecha de creación es obligatoria',
            'fecha_creacion.date' => 'La fecha de creación debe ser una fecha válida',
            'descripcion_ticket.required' => 'La descripción del ticket es obligatoria',
            'descripcion_ticket.max' => 'La descripción no puede exceder 500 caracteres',
            'id_estado_ticket_fk.required' => 'El estado del ticket es obligatorio',
            'id_estado_ticket_fk.exists' => 'El estado del ticket especificado no existe',
            'id_tecnico_fk.required' => 'El técnico asignado es obligatorio',
            'id_tecnico_fk.exists' => 'La persona especificada para técnico no existe',
            'id_cliente_fk.required' => 'El cliente es obligatorio',
            'id_cliente_fk.exists' => 'La persona especificada para cliente no existe'
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
