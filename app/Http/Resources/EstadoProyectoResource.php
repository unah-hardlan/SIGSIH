<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstadoProyectoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_estado_proyecto_pk' => $this->id_estado_proyecto_pk,
            'nombre_estado' => $this->nombre_estado,
            'descripcion_estado_proyecto' => $this->descripcion_estado_proyecto,
        ];
    }
}
