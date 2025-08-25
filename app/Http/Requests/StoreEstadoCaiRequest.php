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
            'nombre_estado_cai' => 'required|string|max:50|unique:tbl_estado_cai,nombre_estado_cai',
            'descripcion_estado_cai' => 'nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_estado_cai.required' => 'El nombre del estado CAI es obligatorio',
            'nombre_estado_cai.unique' => 'Ya existe un estado CAI con ese nombre',
            'nombre_estado_cai.max' => 'El nombre no puede exceder 50 caracteres',
            'descripcion_estado_cai.max' => 'La descripción no puede exceder 255 caracteres'
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
