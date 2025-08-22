<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class UpdateTipoMantenimientoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tipo_mantenimiento' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('tbl_tipo_mantenimiento', 'tipo_mantenimiento')->ignore($this->route('tipo_mantenimiento'), 'id_tipo_mantenimiento_pk')
            ],
            'descripcion_mantenimiento' => 'sometimes|nullable|string|max:255'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'tipo_mantenimiento.required' => 'El tipo de mantenimiento es obligatorio',
            'tipo_mantenimiento.max' => 'El tipo de mantenimiento no puede exceder 50 caracteres',
            'tipo_mantenimiento.unique' => 'Este tipo de mantenimiento ya existe',
            'descripcion_mantenimiento.max' => 'La descripción no puede exceder 255 caracteres'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Error de validación',
            'errors' => $validator->errors()
        ], 422));
    }
}
