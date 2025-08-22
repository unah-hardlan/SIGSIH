<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServicioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id_servicio_pk,
            'nombre_servicio' => $this->nombre_servicio,
            'tarifa' => $this->tarifa
        ];
    }
}
