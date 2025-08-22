<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartamentoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_departamento_pk' => $this->id_departamento_pk,
            'nombre_departamento' => $this->nombre_departamento,
            'id_pais_fk' => $this->id_pais_fk,
            
            // Relaciones
            'pais' => $this->whenLoaded('pais')
        ];
    }
}
