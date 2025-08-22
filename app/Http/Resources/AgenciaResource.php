<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgenciaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_agencias_pk' => $this->id_agencias_pk,
            'nombre_agencia' => $this->nombre_agencia,
            'horario_agencia' => $this->horario_agencia,
            'id_direccion_fk' => $this->id_direccion_fk,
            
            // Relaciones
            'direccion' => $this->whenLoaded('direccion')
        ];
    }
}
