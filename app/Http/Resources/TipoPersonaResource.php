<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TipoPersonaResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id_tipo_persona_pk,
            'nombre_tipo_persona' => $this->nombre_tipo_persona,
            'descripcion' => $this->descripcion,
        ];
    }
}
