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
            // Map actual DB columns to stable API keys used on frontend
            'nombre_estado' => $this->nombre,
            'descripcion_estado' => $this->descripcion,
            'codigo' => $this->codigo,
            'es_final' => (bool) $this->es_final,
            'orden' => $this->orden,
            'total_solicitudes' => $this->whenLoaded('solicitudes', function () {
                return $this->solicitudes->count();
            }),
        ];
    }
}
