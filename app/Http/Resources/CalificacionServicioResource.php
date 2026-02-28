<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CalificacionServicioResource extends JsonResource
{
    
    public function toArray($request): array
    {
        return [
            'id' => $this->id_calificacion_servicio_pk,
            'nombre_calificacion' => $this->nombre_calificacion,
            'descripcion_calificacion' => $this->descripcion_calificacion,
        ];
    }
}
