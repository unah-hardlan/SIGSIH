<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstadoSolicitudResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_estado_solicitud_pk' => $this->id_estado_solicitud_pk,
            
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo' => $this->codigo,
            'orden' => $this->orden,
            'es_final' => (bool) $this->es_final, 

            'total_solicitudes' => $this->whenLoaded('solicitudes', function () {
                return $this->solicitudes->count();
            }),
        ];
    }
}