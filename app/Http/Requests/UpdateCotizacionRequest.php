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
            'valido_hasta' => 'nullable|date|after_or_equal:today',
            'subtotal' => 'sometimes|numeric|min:0',
            'total' => 'sometimes|numeric|gt:0',
            'imponible' => 'sometimes|numeric|min:0',
            'impuesto' => 'sometimes|numeric|min:0',
            'total_impuesto' => 'sometimes|numeric|min:0',
            'otros_cargos' => 'sometimes|numeric|min:0',
            'impuesto_otros' => 'sometimes|numeric|min:0',
            'anticipo_requerido' => 'sometimes|numeric|min:0',
            'id_estado_cotizacion_fk' => 'required|integer|exists:tbl_estado_cotizacion,id_estado_cotizacion_pk',
            'id_cliente_fk' => 'sometimes|integer|exists:tbl_cliente,id_cliente_pk',
        ];
    }

    public function messages(): array
    {
        return [
            'valido_hasta.date' => 'La fecha de validez debe ser una fecha válida.',
            'valido_hasta.after_or_equal' => 'La fecha de validez no puede ser anterior a hoy.',
            'fecha_cotizacion.before_or_equal' => 'La fecha de cotización no puede ser posterior a la fecha de validez.',
            'total.gt' => 'El total de la cotización debe ser mayor a 0.',
            'id_estado_cotizacion_fk.required' => 'El estado de la cotización es obligatorio.',
            'id_estado_cotizacion_fk.integer' => 'El estado de la cotización es inválido.',
            'id_estado_cotizacion_fk.exists' => 'El estado de la cotización no existe.',
        ];
    }

    public function attributes(): array
    {
        return [
            'valido_hasta' => 'válido hasta',
            'id_estado_cotizacion_fk' => 'estado de la cotización',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $fechaCotizacion = $this->input('fecha_cotizacion');
            $validoHasta = $this->input('valido_hasta');

            if ($fechaCotizacion && $validoHasta && $fechaCotizacion > $validoHasta) {
                $validator->errors()->add('fecha_cotizacion', 'La fecha de cotización no puede ser posterior a la fecha de validez.');
            }
        });
    }
}
