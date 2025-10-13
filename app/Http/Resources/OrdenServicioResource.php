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
            'fecha_recepcion' => $this->fecha_recepcion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_finalizacion' => $this->fecha_finalizacion,
            'observaciones' => $this->observaciones,
            'diagnostico_tecnico' => $this->diagnostico_tecnico,
            'diagnostico_cliente' => $this->diagnostico_cliente,
            'id_calificacion_servicio_fk' => $this->id_calificacion_servicio_fk,
            'id_cotizacion_fk' => $this->id_cotizacion_fk,
            // Human-friendly identifiers for frontend selects
            'numero_orden_servicio' => $this->numero_orden_servicio ?? null,
            // Provide a short code and a display name used by the frontend
            'codigo_orden' => $this->numero_orden_servicio ?? null,
            'nombre_orden' => ($this->numero_orden_servicio ?? '') . ($this->solicitudServicio ? ' - ' . substr($this->solicitudServicio->descripcion_problema, 0, 50) : ''),
            
            // Relaciones
            'solicitud_servicio' => $this->whenLoaded('solicitudServicio', function () {
                return [
                    'id_solicitud_pk' => $this->solicitudServicio->id_solicitud_pk,
                    'numero_solicitud_acf' => $this->solicitudServicio->numero_solicitud_acf,
                    'descripcion_problema' => $this->solicitudServicio->descripcion_problema,
                ];
            }),
            'tecnico' => $this->whenLoaded('tecnico', function () {
                return [
                    'id_persona_pk' => $this->tecnico->id_persona_pk,
                    'primer_nombre' => $this->tecnico->primer_nombre,
                    'primer_apellido' => $this->tecnico->primer_apellido,
                    'dni' => $this->tecnico->dni,
                ];
            }),
            'calificacion_servicio' => $this->whenLoaded('calificacionServicio', function () {
                return [
                    'id_calificacion_servicio_pk' => $this->calificacionServicio->id_calificacion_servicio_pk,
                    'calificacion' => $this->calificacionServicio->calificacion,
                    'comentarios' => $this->calificacionServicio->comentarios,
                ];
            }),
            'cotizacion' => $this->whenLoaded('cotizacion', function () {
                return [
                    'id_cotizacion_pk' => $this->cotizacion->id_cotizacion_pk,
                    'numero_cotizacion' => $this->cotizacion->numero_cotizacion,
                    'total' => $this->cotizacion->total,
                ];
            }),
        ];
    }
}
