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
            'id_estado_cai_pk' => $this->id_estado_cai_pk,
            'codigo_estado_cai' => $this->codigo,
            'nombre_estado_cai' => $this->nombre,
            'descripcion_estado_cai' => $this->descripcion,
            'es_final' => (bool) $this->es_final,
            'orden' => (int) ($this->orden ?? 0)
        ];
    }
}
