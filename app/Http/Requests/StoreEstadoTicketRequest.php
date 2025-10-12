<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEstadoTicketRequest extends FormRequest
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
            'codigo' => 'required|string|max:50|unique:tbl_estado_ticket,codigo',
            'nombre' => 'required|string|max:50|unique:tbl_estado_ticket,nombre',
            'descripcion' => 'nullable|string|max:255',
            'es_final' => 'required|boolean',
            'orden' => 'required|integer|min:0',
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
            'nombre.required' => 'El nombre del estado es obligatorio',
            'nombre.max' => 'El nombre del estado no puede exceder 50 caracteres',
            'nombre.unique' => 'Este nombre de estado ya existe',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres',
            'es_final.required' => 'El estado final es obligatorio',
            'orden.required' => 'El orden es obligatorio',
            'orden.min' => 'El orden debe ser un número entero positivo',
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
