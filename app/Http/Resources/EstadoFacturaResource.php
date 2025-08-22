<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EstadoFacturaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id_estado_factura_pk,
            'nombre_estado' => $this->nombre_estado,
            'descripcion_estado_factura' => $this->descripcion_estado_factura
        ];
    }
}
