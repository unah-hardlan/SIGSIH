<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GastoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_gasto_pk' => $this->id_gasto_pk,
            'nombre_gasto' => $this->nombre_gasto,
            'fecha_gasto' => $this->fecha_gasto,
            'monto_gasto' => $this->monto_gasto,
            'descripcion_gasto' => $this->descripcion_gasto,
            'id_proyecto_fk' => $this->id_proyecto_fk,
            'id_categoria_fk' => $this->id_categoria_fk,
            
            // Relaciones
            'proyecto' => $this->whenLoaded('proyecto'),
            'categoria' => $this->whenLoaded('categoria')
        ];
    }
}
