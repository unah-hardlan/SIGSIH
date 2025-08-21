<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CotizacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_cotizacion_pk' => $this->id_cotizacion_pk,
            'fecha_cotizacion' => $this->fecha_cotizacion?->format('Y-m-d H:i:s'),
            'valido_hasta' => $this->valido_hasta?->format('Y-m-d'),
            'subtotal' => (float) $this->subtotal,
            'total' => (float) $this->total,
            'imponible' => (float) $this->imponible,
            'impuesto' => (float) $this->impuesto,
            'total_impuesto' => (float) $this->total_impuesto,
            'otros_cargos' => (float) $this->otros_cargos,
            'anticipo_requerido' => (float) $this->anticipo_requerido,
            'id_cliente_fk' => $this->id_cliente_fk,
            'cliente' => $this->whenLoaded('cliente', function(){
                return [
                    'id_persona_pk' => $this->cliente->id_persona_pk,
                    'primer_nombre' => $this->cliente->primer_nombre,
                    'primer_apellido' => $this->cliente->primer_apellido,
                ];
            })
        ];
    }
}
