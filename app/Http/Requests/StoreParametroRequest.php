<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreParametroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'parametro' => 'required|string|max:100|unique:tbl_parametros,parametro',
            // Valor por defecto acepta cualquier string; podemos especializar abajo
            'valor' => 'required|string|max:255',
            // Si la columna es NOT NULL en la BD se completará automáticamente con el usuario autenticado
            'id_usuario_fk' => 'nullable|integer|exists:tbl_ms_usuario,id_usuario_pk',
        ];

        // Si el parámetro es el correo del admin, validar como email (acepta dominios .com, .es, etc.)
        if (strtoupper($this->input('parametro') ?? '') === 'ADMIN.CORREO') {
            // Usar una validación relajada que acepte cualquier dominio/TLD válido o no estándar.
            // Requerimos que tenga una parte local, una '@' y al menos un '.' en la parte del dominio.
            // Esto permite .com, .es, y TLDs no estándar como '.extension'.
            $rules['valor'] = [
                'required',
                'string',
                'max:255',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/',
            ];
        }

        return $rules;
    }
}
