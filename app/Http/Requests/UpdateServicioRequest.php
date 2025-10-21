<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $servicioId = $this->route('id') ?? $this->route('servicio');
        return [
            'nombre_servicio' => 'sometimes|required|string|max:100|unique:tbl_servicio,nombre_servicio,' . $servicioId . ',id_servicio_pk',
            'tarifa' => 'sometimes|required|numeric|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_servicio.required' => 'El nombre del servicio es obligatorio',
            'nombre_servicio.unique' => 'Ya existe un servicio con ese nombre',
            'nombre_servicio.max' => 'El nombre no puede exceder 100 caracteres',
            'tarifa.required' => 'La tarifa es obligatoria',
            'tarifa.numeric' => 'La tarifa debe ser numérica',
            'tarifa.min' => 'La tarifa no puede ser negativa'
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