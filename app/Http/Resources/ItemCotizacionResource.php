<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemCotizacionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_item_cotizacion_pk' => $this->id_item_cotizacion_pk,
            'descripcion' => $this->descripcion,
            'precio_unitario' => (float) $this->precio_unitario,
            'cantidad' => (float) $this->cantidad,
            'impuesto' => (float) $this->impuesto,
            'total' => (float) $this->total,
            'id_cotizacion_fk' => $this->id_cotizacion_fk,
            'id_producto_fk' => $this->id_producto_fk,
            'cotizacion' => $this->whenLoaded('cotizacion', function () {
                return [
                    'id_cotizacion_pk' => $this->cotizacion->id_cotizacion_pk,
                    'fecha_cotizacion' => $this->cotizacion->fecha_cotizacion?->format('Y-m-d H:i:s'),
                ];
            })
        ];
    }
}
