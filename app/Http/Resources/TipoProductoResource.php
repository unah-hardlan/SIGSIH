<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoProductoResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id_tipo_producto_pk' => $this->id_tipo_producto_pk,
            'nombre_tipo_producto' => $this->nombre_tipo_producto,
            'descripcion_tipo_producto' => $this->descripcion_tipo_producto,
        ];
    }
}
