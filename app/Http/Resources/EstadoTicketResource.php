<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstadoTicketResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_estado_ticket_pk' => $this->id_estado_ticket_pk,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'es_final' => $this->es_final,
            'orden' => $this->orden,
        ];
    }
}
