<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateEstadoTicketRequest extends FormRequest
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
            'nombre_estado' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_estado_ticket', 'nombre_estado')->ignore($this->route('estado_ticket'), 'id_estado_ticket_pk')
            ],
            'descripcion_estado_ticket' => 'sometimes|nullable|string|max:255'
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
            'nombre_estado.required' => 'El nombre del estado es obligatorio',
            'nombre_estado.max' => 'El nombre del estado no puede exceder 50 caracteres',
            'nombre_estado.unique' => 'Este nombre de estado ya existe',
            'descripcion_estado_ticket.max' => 'La descripción no puede exceder 255 caracteres'
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
