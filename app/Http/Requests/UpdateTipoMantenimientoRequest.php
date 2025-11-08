<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTipoMantenimientoRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return true;
    }

    
    public function rules(): array
    {
        
        $routeId = $this->route('tipos_mantenimiento')
            ?? $this->route('tipo_mantenimiento')
            ?? $this->route('id')
            ?? $this->input('id_tipo_mantenimiento_pk');

        return [
            'tipo_mantenimiento' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_tipo_mantenimiento', 'tipo_mantenimiento')
                    ->ignore($routeId, 'id_tipo_mantenimiento_pk')
            ],
            'descripcion_mantenimiento' => 'sometimes|nullable|string|max:255'
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
