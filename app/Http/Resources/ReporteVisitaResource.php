<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReporteVisitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_reportes_pk' => $this->id_reportes_pk,
            'fecha_reporte' => $this->fecha_reporte?->format('Y-m-d H:i:s'),
            'observaciones' => $this->observaciones,
            'id_tipo_visita_fk' => $this->id_tipo_visita_fk,
            'id_servicio_realizado_fk' => $this->id_servicio_realizado_fk,
            'id_accion_realizada_fk' => $this->id_accion_realizada_fk,
            'id_orden_servicio_fk' => $this->id_orden_servicio_fk,
            'tipo_visita' => $this->whenLoaded('tipoVisita', function(){
                return [
                    'id_tipo_visita_pk' => $this->tipoVisita->id_tipo_visita_pk,
                    'nombre_tipo_visita' => $this->tipoVisita->nombre_tipo_visita,
                ];
            }),
            'servicio_realizado' => $this->whenLoaded('servicioRealizado', function(){
                return [
                    'id_servicio_realizado_pk' => $this->servicioRealizado->id_servicio_realizado_pk,
                    'nombre_servicio' => $this->servicioRealizado->nombre_servicio,
                ];
            }),
            'accion_realizada' => $this->whenLoaded('accionRealizada', function(){
                return [
                    'id_accion_realizada_pk' => $this->accionRealizada->id_accion_realizada_pk,
                    'nombre_accion' => $this->accionRealizada->nombre_accion,
                ];
            }),
            'orden_servicio' => $this->whenLoaded('ordenServicio', function(){
                return [
                    'id_orden_servicio_pk' => $this->ordenServicio->id_orden_servicio_pk,
                    'numero_orden_servicio' => $this->ordenServicio->numero_orden_servicio,
                ];
            }),
        ];
    }
}
