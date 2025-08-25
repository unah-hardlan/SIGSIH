<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CiudadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_ciudad_pk' => $this->id_ciudad_pk,
            'nombre_ciudad' => $this->nombre_ciudad,
            'id_departamento_fk' => $this->id_departamento_fk,
            
            // Relaciones
            'departamento' => $this->whenLoaded('departamento')
        ];
    }
}
