<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoObjetoResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id_tipo_objeto_pk,
            'nombre' => $this->nombre_tipo_objeto,
            'descripcion' => $this->descripcion_tipo_objeto,
        ];
    }
}
