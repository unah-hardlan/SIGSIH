<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoCaiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id_estado_cai_pk,
            'nombre_estado_cai' => $this->nombre_estado_cai,
            'descripcion_estado_cai' => $this->descripcion_estado_cai
        ];
    }
}
