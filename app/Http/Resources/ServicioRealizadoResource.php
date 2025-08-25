<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServicioRealizadoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_servicio_realizado_pk' => $this->id_servicio_realizado_pk,
            'nombre_servicio' => $this->nombre_servicio,
            'descripcion_servicio' => $this->descripcion_servicio,
        ];
    }
}
