<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServicioResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id_servicio_pk' => $this->id_servicio_pk,
            'nombre_servicio' => $this->nombre_servicio,
            'tarifa' => $this->tarifa
        ];
    }
}