<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEstadoFacturaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_estado' => 'sometimes|required|string|max:50|unique:tbl_estado_factura,nombre_estado,' . $this->route('estadoFactura'),
            'descripcion_estado_factura' => 'sometimes|nullable|string|max:255'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_estado.required' => 'El nombre del estado es obligatorio',
            'nombre_estado.unique' => 'Ya existe un estado con ese nombre',
            'nombre_estado.max' => 'El nombre no puede exceder 50 caracteres',
            'descripcion_estado_factura.max' => 'La descripción no puede exceder 255 caracteres'
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
