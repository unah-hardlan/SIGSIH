<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DireccionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_direccion_pk' => $this->id_direccion_pk,
            'id_ciudad_fk' => $this->id_ciudad_fk,
            
            // Relaciones
            'ciudad' => $this->whenLoaded('ciudad')
        ];
    }
}
