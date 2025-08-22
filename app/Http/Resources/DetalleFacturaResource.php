<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleFacturaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id_detalle_pk,
            'factura' => $this->factura ? [
                'id' => $this->factura->id_factura_pk,
                'numero' => $this->factura->numero
            ] : null,
            'servicio' => $this->servicio ? [
                'id' => $this->servicio->id_servicio_pk,
                'nombre_servicio' => $this->servicio->nombre_servicio,
                'tarifa' => $this->servicio->tarifa
            ] : null,
            'fecha_servicio' => $this->fecha_servicio,
            'horas' => $this->horas,
            'descuento' => $this->descuento
        ];
    }
}
