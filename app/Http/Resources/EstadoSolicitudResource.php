<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstadoSolicitudResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_estado_solicitud_pk,
            'nombre_estado' => $this->nombre_estado,
            'descripcion_estado' => $this->descripcion_estado,
            'total_solicitudes' => $this->whenLoaded('solicitudes', function () {
                return $this->solicitudes->count();
            }),
        ];
    }
}
