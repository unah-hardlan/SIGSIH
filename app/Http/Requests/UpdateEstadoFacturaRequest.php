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
        $estadoId = $this->route('id') ?? $this->route('estadoFactura');
        return [
            'nombre' => 'sometimes|required|string|max:50|unique:tbl_estado_factura,nombre,' . $estadoId . ',id_estado_factura_pk',
            'descripcion' => 'sometimes|nullable|string|max:255',
            'codigo' => 'sometimes|nullable|string|max:10',
            'es_final' => 'sometimes|boolean',
            'orden' => 'sometimes|integer|min:0'
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del estado es obligatorio',
            'nombre.unique' => 'Ya existe un estado con ese nombre',
            'nombre.max' => 'El nombre no puede exceder 50 caracteres',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres',
            'codigo.max' => 'El código no puede exceder 10 caracteres'
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