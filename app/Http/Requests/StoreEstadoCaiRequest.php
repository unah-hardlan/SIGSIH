<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEstadoCaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo_estado_cai' => 'nullable|string|max:10',
            'nombre_estado_cai' => 'required|string|max:50',
            'descripcion_estado_cai' => 'nullable|string|max:255',
            'es_final' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo_estado_cai.unique' => 'Ya existe un estado CAI con ese código',
            'codigo_estado_cai.max' => 'El código no puede exceder 10 caracteres',
            'nombre_estado_cai.required' => 'El nombre del estado CAI es obligatorio',
            'nombre_estado_cai.unique' => 'Ya existe un estado CAI con ese nombre',
            'nombre_estado_cai.max' => 'El nombre no puede exceder 50 caracteres',
            'descripcion_estado_cai.max' => 'La descripción no puede exceder 255 caracteres',
            'orden.min' => 'El orden debe ser un número positivo'
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
