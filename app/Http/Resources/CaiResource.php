<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CaiResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id' => $this->id_cai_pk,
            'codigo' => $this->codigo,
            'rango_inicio' => $this->rango_inicio,
            'rango_fin' => $this->rango_fin,
            'consecutivo_actual' => $this->consecutivo_actual,
            'fecha_limite' => $this->fecha_limite,
            'id_estado_cai_fk' => $this->id_estado_cai_fk,
            'estado_cai' => $this->whenLoaded('estadoCai', function () {
                return [
                    'id' => $this->estadoCai->id_estado_cai_pk,
                    'nombre' => $this->estadoCai->nombre_estado_cai ?? $this->estadoCai->nombre,
                    'descripcion' => $this->estadoCai->descripcion_estado_cai ?? $this->estadoCai->descripcion,
                    'es_final' => $this->estadoCai->es_final
                ];
            })
        ];
    }
}
