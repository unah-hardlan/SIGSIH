<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetalleFacturaResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id' => $this->id_detalle_pk,
            'id_detalle_pk' => $this->id_detalle_pk,
            'id_factura_fk' => $this->id_factura_fk,
            'id_servicio_fk' => $this->id_servicio_fk,
            'descripcion' => $this->descripcion,
            'precio_unitario' => $this->precio_unitario,
            'cantidad' => $this->cantidad,
            'impuesto' => $this->impuesto,
            'total_linea' => $this->total_linea,
            'fecha_servicio' => $this->fecha_servicio ? 
                (is_string($this->fecha_servicio) ? $this->fecha_servicio : $this->fecha_servicio->format('Y-m-d')) 
                : null,
            'horas' => $this->horas,
            'descuento' => $this->descuento,
            
            
            'factura' => $this->whenLoaded('factura', function () {
                if (!$this->factura) return null;
                return [
                    'id' => $this->factura->id_factura_pk,
                    'numero_factura' => $this->factura->numero,
                    'fecha' => is_string($this->factura->fecha) ? $this->factura->fecha : ($this->factura->fecha ? $this->factura->fecha->format('Y-m-d') : null)
                ];
            }),
            
            'servicio' => $this->whenLoaded('servicio', function () {
                if (!$this->servicio) return null;
                return [
                    'id' => $this->servicio->id_servicio_pk,
                    'nombre_servicio' => $this->servicio->nombre_servicio,
                    'tarifa' => $this->servicio->tarifa
                ];
            }),
            
            
            'factura_numero' => $this->whenLoaded('factura', function () {
                return $this->factura ? $this->factura->numero : 'Sin factura';
            }),
            
            'servicio_nombre' => $this->whenLoaded('servicio', function () {
                return $this->servicio ? $this->servicio->nombre_servicio : 'Sin servicio';
            })
        ];
    }
}
