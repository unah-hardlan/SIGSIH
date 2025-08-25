<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReporteVisitaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'fecha_reporte' => 'nullable|date',
            'observaciones' => 'nullable|string|max:500',
            'id_tipo_visita_fk' => 'required|integer|exists:tbl_tipo_visita,id_tipo_visita_pk',
            'id_servicio_realizado_fk' => 'required|integer|exists:tbl_servicio_realizado,id_servicio_realizado_pk',
            'id_accion_realizada_fk' => 'required|integer|exists:tbl_accion_realizada,id_accion_realizada_pk',
            'id_orden_servicio_fk' => 'required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
        ];
    }
}
