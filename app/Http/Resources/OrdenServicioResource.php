<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdenServicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_orden_servicio_pk' => $this->id_orden_servicio_pk,
            'id_solicitud_servicio_fk' => $this->id_solicitud_servicio_fk,
            'id_tecnico_fk' => $this->id_tecnico_fk,
            'numero_orden_servicio' => $this->numero_orden_servicio,
            'id_estado_orden_servicio_fk' => $this->id_estado_orden_servicio_fk,
            'fecha_creada' => $this->fecha_creada,
            'fecha_asignada' => $this->fecha_asignada,
            'fecha_recepcion' => $this->fecha_recepcion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_finalizacion' => $this->fecha_finalizacion,
            'observaciones' => $this->observaciones,
            'diagnostico_tecnico' => $this->diagnostico_tecnico,
            'diagnostico_cliente' => $this->diagnostico_cliente,
            'id_cotizacion_fk' => $this->id_cotizacion_fk,
            // Human-friendly identifiers for frontend selects
            'numero_orden_servicio' => $this->numero_orden_servicio ?? null,
            // Provide a short code and a display name used by the frontend
            'codigo_orden' => $this->numero_orden_servicio ?? null,
            'nombre_orden' => ($this->numero_orden_servicio ?? '') . ($this->solicitudServicio ? ' - ' . substr($this->solicitudServicio->descripcion_problema, 0, 50) : ''),
            
            // Relaciones
            'solicitud_servicio' => $this->whenLoaded('solicitudServicio', function () {
                return array_filter([
                    'id_solicitud_pk' => $this->solicitudServicio->id_solicitud_pk,
                    'numero_solicitud_acf' => $this->solicitudServicio->numero_solicitud_acf,
                    'numero_solicitud_cliente' => $this->solicitudServicio->numero_solicitud_cliente,
                    'descripcion_problema' => $this->solicitudServicio->descripcion_problema,
                    'cliente' => $this->solicitudServicio->relationLoaded('cliente') && $this->solicitudServicio->cliente
                        ? [
                            'id_cliente_pk' => $this->solicitudServicio->cliente->id_cliente_pk,
                            'tipo_cliente' => $this->solicitudServicio->cliente->tipo_cliente,
                            'estado_cliente' => $this->solicitudServicio->cliente->estado_cliente,
                            'empresa' => $this->solicitudServicio->cliente->relationLoaded('empresa') && $this->solicitudServicio->cliente->empresa
                                ? [
                                    'nombre_comercial' => $this->solicitudServicio->cliente->empresa->nombre_comercial,
                                    'razon_social' => $this->solicitudServicio->cliente->empresa->razon_social,
                                    'rtn' => $this->solicitudServicio->cliente->empresa->rtn,
                                ]
                                : null,
                        ]
                        : null,
                    'contacto' => $this->solicitudServicio->relationLoaded('contacto') && $this->solicitudServicio->contacto
                        ? [
                            'id_contacto_pk' => $this->solicitudServicio->contacto->id_contacto_pk,
                            'tipo_contacto' => $this->solicitudServicio->contacto->tipo_contacto,
                            'valor_contacto' => $this->solicitudServicio->contacto->valor_contacto,
                        ]
                        : null,
                ], fn ($value) => $value !== null);
            }),
            'tecnico' => $this->whenLoaded('tecnico', function () {
                return [
                    'id_persona_pk' => $this->tecnico->id_persona_pk,
                    'primer_nombre' => $this->tecnico->primer_nombre,
                    'primer_apellido' => $this->tecnico->primer_apellido,
                    'dni' => $this->tecnico->dni,
                ];
            }),
            'estado' => $this->whenLoaded('estado', function () {
                return [
                    'id_estado_orden_servicio_pk' => $this->estado->id_estado_orden_servicio_pk,
                    // DB column is 'nombre'; keep API key 'nombre_estado' for frontend compatibility
                    'nombre_estado' => $this->estado->nombre,
                    'codigo' => $this->estado->codigo,
                ];
            }),
            'cotizacion' => $this->whenLoaded('cotizacion', function () {
                return [
                    'id_cotizacion_pk' => $this->cotizacion->id_cotizacion_pk,
                    'fecha_cotizacion' => $this->cotizacion->fecha_cotizacion?->format('Y-m-d H:i:s'),
                    'total' => (float) $this->cotizacion->total,
                ];
            }),
            'cotizacion_generada' => $this->whenLoaded('cotizacionGenerada', function () {
                return [
                    'id_cotizacion_pk' => $this->cotizacionGenerada->id_cotizacion_pk,
                    'fecha_cotizacion' => $this->cotizacionGenerada->fecha_cotizacion?->format('Y-m-d H:i:s'),
                    'total' => (float) $this->cotizacionGenerada->total,
                ];
            }),
        ];
    }
}
