<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCaiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50|unique:tbl_cai,codigo',
            'rango_inicio' => 'required|string|max:20',
            'rango_fin' => 'required|string|max:20',
            'consecutivo_actual' => 'required|integer|min:0',
            'fecha_limite' => 'required|date',
            'id_estado_cai_fk' => 'required|integer|exists:tbl_estado_cai,id_estado_cai_pk'
        ];
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'Ya existe un CAI con ese código',
            'codigo.max' => 'El código no puede exceder 50 caracteres',
            'rango_inicio.required' => 'El rango de inicio es obligatorio',
            'rango_inicio.max' => 'El rango de inicio no puede exceder 20 caracteres',
            'rango_fin.required' => 'El rango de fin es obligatorio',
            'rango_fin.max' => 'El rango de fin no puede exceder 20 caracteres',
            'fecha_limite.required' => 'La fecha límite es obligatoria',
            'fecha_limite.date' => 'La fecha límite debe ser una fecha válida',
            'id_estado_cai_fk.required' => 'El estado CAI es obligatorio',
            'id_estado_cai_fk.exists' => 'El estado CAI especificado no existe'
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
