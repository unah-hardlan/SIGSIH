<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'usuario' => 'required|string|max:50|unique:tbl_ms_usuario,usuario',
            'nombre_usuario' => 'required|string|max:100',
            'correo_electronico' => 'required|email|max:100|unique:tbl_ms_usuario,correo_electronico',
            'contrasena' => 'required|string|min:8',
            'estado_usuario' => 'nullable|string|max:20',
            'primer_ingreso' => 'nullable|boolean',
            'fecha_ultima_conexion' => 'nullable|date',
            'fecha_vencimiento' => 'nullable|date',
        ];
    }
}
