<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemCotizacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'descripcion' => 'required|string|max:255',
            'precio_unitario' => 'required|numeric|min:0',
            'cantidad' => 'required|numeric|min:0',
            'impuesto' => 'nullable|numeric|min:0',
            'total' => 'nullable|numeric|min:0', // se recalcula, pero validamos si viene
            'id_cotizacion_fk' => 'required|integer|exists:tbl_cotizacion,id_cotizacion_pk',
        ];
    }
}
