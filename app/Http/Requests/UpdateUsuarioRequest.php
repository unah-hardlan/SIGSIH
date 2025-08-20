<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUsuarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('usuario') ?? $this->route('id');
        return [
            'nombre_usuario' => 'sometimes|string|max:100',
            'correo_electronico' => "sometimes|email|max:100|unique:tbl_ms_usuario,correo_electronico,{$id},id_usuario_pk",
            'contrasena' => 'sometimes|string|min:8',
            'estado_usuario' => 'sometimes|string|max:20',
            'primer_ingreso' => 'sometimes|boolean',
            'fecha_ultima_conexion' => 'sometimes|date',
            'fecha_vencimiento' => 'sometimes|date',
        ];
    }
}
