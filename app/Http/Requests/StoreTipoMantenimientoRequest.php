<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTipoMantenimientoRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'tipo_mantenimiento' => 'required|string|max:50|unique:tbl_tipo_mantenimiento,tipo_mantenimiento',
            'descripcion_mantenimiento' => 'nullable|string|max:255'
        ];
    }

    
    public function messages(): array
    {
        return [
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio',
            'tipo_mantenimiento.max' => 'El tipo de mantenimiento no puede exceder 50 caracteres',
            'tipo_mantenimiento.unique' => 'Este tipo de mantenimiento ya existe',
            'descripcion_mantenimiento.max' => 'La descripción no puede exceder 255 caracteres'
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
