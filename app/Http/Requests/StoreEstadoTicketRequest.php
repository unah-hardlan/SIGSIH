<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEstadoTicketRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        return [
            'codigo' => 'required|string|max:50|unique:tbl_estado_ticket,codigo',
            'nombre' => 'required|string|max:50|unique:tbl_estado_ticket,nombre',
            'descripcion' => 'nullable|string|max:255',
            'es_final' => 'required|boolean',
            'orden' => 'required|integer|min:0',
        ];
    }

    
    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del estado es obligatorio',
            'nombre.max' => 'El nombre del estado no puede exceder 50 caracteres',
            'nombre.unique' => 'Este nombre de estado ya existe',
            'descripcion.max' => 'La descripción no puede exceder 255 caracteres',
            'es_final.required' => 'El estado final es obligatorio',
            'orden.required' => 'El orden es obligatorio',
            'orden.min' => 'El orden debe ser un número entero positivo',
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
