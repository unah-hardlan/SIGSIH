<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKardexRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_producto_fk' => 'sometimes|integer|exists:tbl_producto,id_producto_pk',
            'id_tipo_movimiento_fk' => 'sometimes|integer|exists:tbl_tipo_movimiento,id_tipo_movimiento_pk',
            'cantidad' => 'sometimes|integer|min:1',
            'fecha_movimiento' => 'sometimes|date',
            'motivo' => 'sometimes|nullable|string|max:255',
            'id_tecnico_fk' => 'sometimes|nullable|integer|exists:tbl_persona,id_persona_pk',
        ];
    }
}
