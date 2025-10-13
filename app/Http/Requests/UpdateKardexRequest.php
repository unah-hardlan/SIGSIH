<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UpdateKardexRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'id_origen_fk' => 'sometimes|nullable|integer|exists:tbl_origen,id_origen_pk',
            'id_producto_fk' => 'sometimes|required|integer|exists:tbl_producto,id_producto_pk',
            'id_tipo_movimiento_fk' => 'sometimes|required|integer|exists:tbl_tipo_movimiento,id_tipo_movimiento_pk',
            'cantidad' => 'sometimes|required|numeric|min:0.001',
            'fecha_movimiento' => 'sometimes|required|date',
            'motivo' => 'sometimes|required|string|max:255',
        ];
    }
}