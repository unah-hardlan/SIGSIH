<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKardexRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'id_producto_fk' => 'required|integer|exists:tbl_producto,id_producto_pk',
            'id_tipo_movimiento_fk' => 'required|integer|exists:tbl_tipo_movimiento,id_tipo_movimiento_pk',
            'cantidad' => 'required|integer|min:1',
            'fecha_movimiento' => 'nullable|date',
            'motivo' => 'nullable|string|max:255',
            'id_tecnico_fk' => 'nullable|integer|exists:tbl_persona,id_persona_pk',
        ];
    }
}
