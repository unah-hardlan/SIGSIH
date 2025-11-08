<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProyectoResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_proyecto_pk' => $this->id_proyecto_pk,
            'nombre_proyecto' => $this->nombre_proyecto,
            'fecha_inicio_proyecto' => $this->fecha_inicio_proyecto,
            'fecha_estimada_fin_proyecto' => $this->fecha_estimada_fin_proyecto,
            'fecha_finalizacion_proyecto' => $this->fecha_finalizacion_proyecto,
            'descripcion_proyecto' => $this->descripcion_proyecto,
            'id_orden_servicio_fk' => $this->id_orden_servicio_fk,
            'id_estado_proyecto_fk' => $this->id_estado_proyecto_fk,
            
            
            'orden_servicio' => $this->whenLoaded('ordenServicio', function () {
                return [
                    'id_orden_servicio_pk' => $this->ordenServicio->id_orden_servicio_pk,
                    'numero_orden_servicio' => $this->ordenServicio->numero_orden_servicio,
                    'nombre_orden_servicio' => $this->ordenServicio->numero_orden_servicio . 
                        ($this->ordenServicio->solicitudServicio ? ' - ' . substr($this->ordenServicio->solicitudServicio->descripcion_problema, 0, 50) : ''),
                    'fecha_recepcion' => $this->ordenServicio->fecha_recepcion,
                    'observaciones' => $this->ordenServicio->observaciones,
                    'solicitud_servicio' => $this->when($this->ordenServicio->relationLoaded('solicitudServicio'), function () {
                        return $this->ordenServicio->solicitudServicio ? [
                            'numero_solicitud_acf' => $this->ordenServicio->solicitudServicio->numero_solicitud_acf,
                            'numero_solicitud_cliente' => $this->ordenServicio->solicitudServicio->numero_solicitud_cliente,
                            'descripcion_problema' => $this->ordenServicio->solicitudServicio->descripcion_problema,
                        ] : null;
                    }),
                ];
            }),
            'estado_proyecto' => $this->whenLoaded('estadoProyecto', function () {
                return [
                    'id_estado_proyecto_pk' => $this->estadoProyecto->id_estado_proyecto_pk,
                    'codigo' => $this->estadoProyecto->codigo,
                    
                    'nombre' => $this->estadoProyecto->nombre,
                    'nombre_estado' => $this->estadoProyecto->nombre,
                    'descripcion_estado_proyecto' => $this->estadoProyecto->descripcion,
                ];
            }),
        ];
    }
}
