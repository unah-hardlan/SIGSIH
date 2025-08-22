<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleOrdenProductoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id_detalle_pk,
            'orden_servicio' => $this->ordenServicio ? [
                'id' => $this->ordenServicio->id_orden_servicio_pk,
                'codigo' => $this->ordenServicio->codigo ?? null
            ] : null,
            'producto' => $this->producto ? [
                'id' => $this->producto->id_producto_pk,
                'nombre' => $this->producto->nombre ?? null
            ] : null,
            'cantidad' => $this->cantidad
        ];
    }
}
