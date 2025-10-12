<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateEstadoCalendarioRequest extends FormRequest
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
        // Para apiResource 'estados-calendario', Laravel convierte a {estados_calendario}
        $id = $this->route('estados_calendario');
        
        return [
            'codigo' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_estado_calendario', 'codigo')->ignore($id, 'id_estado_calendario_pk')
            ],
            'nombre' => [
                'sometimes',
                'required',
                'string',
                'max:100',
                Rule::unique('tbl_estado_calendario', 'nombre')->ignore($id, 'id_estado_calendario_pk')
            ],
            'descripcion' => 'sometimes|nullable|string|max:255',
            'es_final' => 'sometimes|required|boolean',
            'orden' => 'sometimes|required|integer|min:0',
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
            'codigo.required' => 'El código es obligatorio',
            'codigo.max' => 'El código no puede exceder 50 caracteres',
            'codigo.unique' => 'Este código ya existe',
            'nombre.required' => 'El nombre del estado es obligatorio',
            'nombre.max' => 'El nombre del estado no puede exceder 100 caracteres',
            'nombre.unique' => 'Este nombre de estado ya existe',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres',
            'es_final.required' => 'Debe especificar si es final',
            'es_final.boolean' => 'El valor de es_final debe ser verdadero o falso',
            'orden.required' => 'El orden es obligatorio',
            'orden.integer' => 'El orden debe ser un número entero',
            'orden.min' => 'El orden debe ser mayor o igual a 0',
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