<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProyectoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
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
            
            // Relaciones
            'orden_servicio' => $this->whenLoaded('ordenServicio', function () {
                return [
                    'id_orden_servicio_pk' => $this->ordenServicio->id_orden_servicio_pk,
                    'fecha_recepcion' => $this->ordenServicio->fecha_recepcion,
                    'observaciones' => $this->ordenServicio->observaciones,
                ];
            }),
            'estado_proyecto' => $this->whenLoaded('estadoProyecto', function () {
                return [
                    'id_estado_proyecto_pk' => $this->estadoProyecto->id_estado_proyecto_pk,
                    'nombre_estado' => $this->estadoProyecto->nombre_estado,
                    'descripcion_estado_proyecto' => $this->estadoProyecto->descripcion_estado_proyecto,
                ];
            }),
        ];
    }
}
