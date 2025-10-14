<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KardexResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_kardex_pk' => $this->id_kardex_pk,
            'id_origen_fk' => $this->id_origen_fk,
            'id_producto_fk' => $this->id_producto_fk,
            'id_tipo_movimiento_fk' => $this->id_tipo_movimiento_fk,
            'cantidad' => (float) $this->cantidad,
            'fecha_movimiento' => $this->fecha_movimiento?->format('Y-m-d'),
            'motivo' => $this->motivo,
            
            'producto' => $this->whenLoaded('producto', fn() => [
                'id_producto_pk' => $this->producto->id_producto_pk,
                'nombre_producto' => $this->producto->nombre_producto,
            ]),
            
            'tipo_movimiento' => $this->whenLoaded('tipoMovimiento', fn() => [
                'id_tipo_movimiento_pk' => $this->tipoMovimiento->id_tipo_movimiento_pk,
                'nombre' => $this->tipoMovimiento->nombre_tipo_movimiento, 
            ]),

            'origen' => $this->whenLoaded('origen', fn() => [
                'id_origen_pk' => $this->origen->id_origen_pk,
                'nombre_origen' => $this->origen->nombre_origen,
            ]),
        ];
    }
}