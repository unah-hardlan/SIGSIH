<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductoResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_producto_pk' => $this->id_producto_pk,
            'sku' => $this->sku,
            'nombre_producto' => $this->nombre_producto,
            'descripcion_producto' => $this->descripcion_producto,
            'precio_unitario' => (float) $this->precio_unitario,
            'precio_costo' => (float) $this->precio_costo,
            'precio_venta' => (float) $this->precio_venta,
            'stock_minimo' => $this->stock_minimo,
            'fecha_registro' => optional($this->fecha_registro)->toDateTimeString(),
            'id_tipo_producto_fk' => $this->id_tipo_producto_fk,
            'tipo_producto' => $this->whenLoaded('tipoProducto', fn() => [
                'id_tipo_producto_pk' => $this->tipoProducto->id_tipo_producto_pk,
                'nombre_tipo_producto' => $this->tipoProducto->nombre_tipo_producto,
            ]),
        ];
    }
}