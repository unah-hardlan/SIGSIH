<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoMovimientoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_tipo_movimiento_pk' => $this->id_tipo_movimiento_pk,
            'nombre_tipo_movimiento' => $this->nombre_tipo_movimiento,
            'descripcion_tipo_movimiento' => $this->descripcion_tipo_movimiento,
        ];
    }
}
