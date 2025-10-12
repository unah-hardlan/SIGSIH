<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EstadoProyectoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_estado_proyecto_pk' => $this->id_estado_proyecto_pk,
            'codigo' => $this->codigo,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'es_final' => (bool) $this->es_final,
            'orden' => $this->orden,
        ];
    }
}