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
            'creado_por' => $this->creado_por,
            'fecha_creacion' => $this->fecha_creacion,
            'modificado_por' => $this->modificado_por,
            'fecha_modificacion' => $this->fecha_modificacion,
        ];
    }
}
