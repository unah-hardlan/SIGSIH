<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemCotizacionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'descripcion' => 'sometimes|string|max:255',
            'precio_unitario' => 'sometimes|numeric|min:0',
            'cantidad' => 'sometimes|numeric|min:0',
            'impuesto' => 'sometimes|numeric|min:0',
            'total' => 'sometimes|numeric|min:0',
            'id_cotizacion_fk' => 'sometimes|integer|exists:tbl_cotizacion,id_cotizacion_pk',
        ];
    }
}
