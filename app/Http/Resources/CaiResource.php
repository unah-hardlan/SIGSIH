<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CaiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id_cai_pk,
            'codigo' => $this->codigo,
            'rango_inicio' => $this->rango_inicio,
            'rango_fin' => $this->rango_fin,
            'fecha_limite' => $this->fecha_limite,
            'estado_cai' => $this->estadoCai ? [
                'id' => $this->estadoCai->id_estado_cai_pk,
                'nombre_estado_cai' => $this->estadoCai->nombre_estado_cai,
                'descripcion_estado_cai' => $this->estadoCai->descripcion_estado_cai
            ] : null
        ];
    }
}
