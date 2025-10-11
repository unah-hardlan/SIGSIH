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
            'calle' => $this->calle,
            'numero' => $this->numero,
            'colonia' => $this->colonia,
            'codigo_postal' => $this->codigo_postal,
            'referencia' => $this->referencia,
            'direccion_completa' => $this->direccion_completa,
            
            // Relaciones
            'ciudad' => $this->whenLoaded('ciudad'),
            'agencia' => $this->whenLoaded('agencia')
        ];
    }
}
