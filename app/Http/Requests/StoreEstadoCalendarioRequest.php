<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEstadoCalendarioRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
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

    
    public function messages(): array
    {
        return [
            'nombre_estado.required' => 'El nombre del estado es obligatorio',
            'nombre_estado.max' => 'El nombre del estado no puede exceder 50 caracteres',
            'nombre_estado.unique' => 'Este nombre de estado ya existe',
            'descripcion_estado_calendario.max' => 'La descripción no puede exceder 255 caracteres'
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
