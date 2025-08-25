<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_ticket_pk' => $this->id_ticket_pk,
            'fecha_creacion' => $this->fecha_creacion?->format('Y-m-d H:i:s'),
            'descripcion_ticket' => $this->descripcion_ticket,
            'id_estado_ticket_fk' => $this->id_estado_ticket_fk,
            'id_tecnico_fk' => $this->id_tecnico_fk,
            'id_cliente_fk' => $this->id_cliente_fk,
            
            // Relaciones
            'estado' => $this->whenLoaded('estado'),
            'tecnico' => $this->whenLoaded('tecnico'),
            'cliente' => $this->whenLoaded('cliente'),
        ];
    }
}
