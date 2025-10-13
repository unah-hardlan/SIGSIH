<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class StoreKardexRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'id_origen_fk' => 'nullable|integer|exists:tbl_origen,id_origen_pk',
            'id_producto_fk' => 'required|integer|exists:tbl_producto,id_producto_pk',
            'id_tipo_movimiento_fk' => 'required|integer|exists:tbl_tipo_movimiento,id_tipo_movimiento_pk',
            'cantidad' => 'required|numeric|min:0.001',
            'fecha_movimiento' => 'required|date',
            'motivo' => 'required|string|max:255',
        ];
    }
}