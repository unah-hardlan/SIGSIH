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
            'otros_cargos' => $this->otros_cargos !== null ? (float) $this->otros_cargos : null,
            'impuesto_otros' => $this->impuesto_otros !== null ? (float) $this->impuesto_otros : null,
            'anticipo_requerido' => $this->anticipo_requerido !== null ? (float) $this->anticipo_requerido : null,
            'id_estado_cotizacion_fk' => $this->id_estado_cotizacion_fk,
            'id_cliente_fk' => $this->id_cliente_fk,
            'cliente_nombre' => $this->whenLoaded('cliente', function () {
                if (!$this->cliente) return null;
                // Empresa
                if ($this->cliente->relationLoaded('empresa') && $this->cliente->empresa) {
                    return $this->cliente->empresa->nombre_comercial
                        ?? $this->cliente->empresa->razon_social
                        ?? null;
                }
                // Persona (puede venir por belongsToMany personas)
                if ($this->cliente->relationLoaded('personas') && $this->cliente->personas && $this->cliente->personas->count()) {
                    $p = $this->cliente->personas->first();
                    return trim(($p->primer_nombre . ' ' . $p->segundo_nombre . ' ' . $p->primer_apellido . ' ' . $p->segundo_apellido));
                }
                return null;
            }),
            'estado' => $this->whenLoaded('estado', function () {
                return [
                    'id_estado_cotizacion_pk' => $this->estado->id_estado_cotizacion_pk,
                    'nombre_estado' => $this->estado->nombre,
                    'codigo' => $this->estado->codigo,
                ];
            }),
            'cliente' => $this->whenLoaded('cliente', function () {
                return [
                    'id_cliente_pk' => $this->cliente->id_cliente_pk,
                    'tipo_cliente' => $this->cliente->tipo_cliente,
                    'estado_cliente' => $this->cliente->estado_cliente,
                    'fecha_registro' => $this->cliente->fecha_registro?->toDateTimeString(),
                    'empresa' => $this->cliente->relationLoaded('empresa') && $this->cliente->empresa
                        ? [
                            'nombre_comercial' => $this->cliente->empresa->nombre_comercial,
                            'razon_social' => $this->cliente->empresa->razon_social,
                            'rtn' => $this->cliente->empresa->rtn,
                        ]
                        : null,
                ];
            })
        ];
    }
}
