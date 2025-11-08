<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoFacturaResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id' => $this->id_estado_factura_pk,
            'id_estado_factura_pk' => $this->id_estado_factura_pk,
            'codigo' => $this->codigo,
            'nombre_estado' => $this->nombre,
            'descripcion_estado_factura' => $this->descripcion,
            'es_final' => $this->es_final,
            'orden' => $this->orden
        ];
    }
}