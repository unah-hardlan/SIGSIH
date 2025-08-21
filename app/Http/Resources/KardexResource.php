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
            'id_producto_fk' => $this->id_producto_fk,
            'id_tipo_movimiento_fk' => $this->id_tipo_movimiento_fk,
            'cantidad' => (int) $this->cantidad,
            'fecha_movimiento' => $this->fecha_movimiento?->format('Y-m-d H:i:s'),
            'motivo' => $this->motivo,
            'id_tecnico_fk' => $this->id_tecnico_fk,
            'producto' => $this->whenLoaded('producto', function(){
                return [
                    'id_producto_pk' => $this->producto->id_producto_pk,
                    'nombre_producto' => $this->producto->nombre_producto,
                ];
            }),
            'tipo_movimiento' => $this->whenLoaded('tipoMovimiento', function(){
                return [
                    'id_tipo_movimiento_pk' => $this->tipoMovimiento->id_tipo_movimiento_pk,
                    'nombre_tipo_movimiento' => $this->tipoMovimiento->nombre_tipo_movimiento,
                ];
            }),
            'tecnico' => $this->whenLoaded('tecnico', function(){
                return [
                    'id_persona_pk' => $this->tecnico->id_persona_pk,
                    'primer_nombre' => $this->tecnico->primer_nombre,
                    'primer_apellido' => $this->tecnico->primer_apellido,
                ];
            }),
        ];
    }
}
