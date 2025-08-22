<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateHistorialContrasenaRequest extends FormRequest
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
            'contrasena' => [
                'sometimes',
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/', // Contraseña segura
            ],
            'id_usuario_fk' => 'sometimes|required|integer|exists:tbl_ms_usuario,id_usuario_pk',
            'creado_por' => 'sometimes|required|string|max:50',
            'fecha_creacion' => 'sometimes|required|date',
            'modificado_por' => 'sometimes|nullable|string|max:50',
            'fecha_modificacion' => 'sometimes|nullable|date|after_or_equal:fecha_creacion'
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
            'contrasena.required' => 'La contraseña es obligatoria',
            'contrasena.min' => 'La contraseña debe tener al menos 8 caracteres',
            'contrasena.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula, un número y un carácter especial',
            'id_usuario_fk.required' => 'El ID del usuario es obligatorio',
            'id_usuario_fk.exists' => 'El usuario especificado no existe',
            'creado_por.required' => 'El campo creado por es obligatorio',
            'creado_por.max' => 'El campo creado por no puede exceder 50 caracteres',
            'fecha_creacion.required' => 'La fecha de creación es obligatoria',
            'fecha_creacion.date' => 'La fecha de creación debe ser una fecha válida',
            'modificado_por.max' => 'El campo modificado por no puede exceder 50 caracteres',
            'fecha_modificacion.date' => 'La fecha de modificación debe ser una fecha válida',
            'fecha_modificacion.after_or_equal' => 'La fecha de modificación debe ser posterior o igual a la fecha de creación'
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
