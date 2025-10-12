<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEstadoCalendarioRequest extends FormRequest
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
            'codigo' => 'required|string|max:50|unique:tbl_estado_calendario,codigo',
            'nombre' => 'required|string|max:100|unique:tbl_estado_calendario,nombre',
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
            'nombre_estado.required' => 'El nombre del estado es obligatorio',
            'nombre_estado.max' => 'El nombre del estado no puede exceder 50 caracteres',
            'nombre_estado.unique' => 'Este nombre de estado ya existe',
            'descripcion_estado_calendario.max' => 'La descripción no puede exceder 255 caracteres'
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
