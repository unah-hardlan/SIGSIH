<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCotizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_cotizacion' => 'sometimes|date',
            'valido_hasta' => 'sometimes|date|after_or_equal:today',
            'subtotal' => 'sometimes|numeric|min:0',
            'total' => 'sometimes|numeric|min:0',
            'imponible' => 'sometimes|numeric|min:0',
            'impuesto' => 'sometimes|numeric|min:0',
            'total_impuesto' => 'sometimes|numeric|min:0',
            'otros_cargos' => 'sometimes|numeric|min:0',
            'impuesto_otros' => 'sometimes|numeric|min:0',
            'anticipo_requerido' => 'sometimes|numeric|min:0',
            'id_estado_cotizacion_fk' => 'sometimes|integer|exists:tbl_estado_cotizacion,id_estado_cotizacion_pk',
            'id_cliente_fk' => 'sometimes|integer|exists:tbl_cliente,id_cliente_pk',
        ];
    }
}
