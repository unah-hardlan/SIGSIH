<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCotizacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_cotizacion' => 'nullable|date',
            'valido_hasta' => 'required|date|after_or_equal:today',
            'subtotal' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'imponible' => 'required|numeric|min:0',
            'impuesto' => 'required|numeric|min:0',
            'total_impuesto' => 'required|numeric|min:0',
            'otros_cargos' => 'nullable|numeric|min:0',
            'impuesto_otros' => 'nullable|numeric|min:0',
            'anticipo_requerido' => 'nullable|numeric|min:0',
            'id_cliente_fk' => 'required|integer|exists:tbl_cliente,id_cliente_pk',
        ];
    }
}
