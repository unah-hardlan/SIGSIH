<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RolResource extends JsonResource
{
    
    public function toArray($request): array
    {
        return [
            'id' => $this->id_rol_pk,
            'rol' => $this->rol,
            'descripcion_rol' => $this->descripcion_rol,
            'creado_por' => $this->creado_por,
            'fecha_creacion' => optional($this->fecha_creacion)->toDateTimeString(),
            'fecha_creacion_formatted' => \App\Helpers\DateHelper::format($this->fecha_creacion),
            'modificado_por' => $this->modificado_por,
            'fecha_modificacion' => optional($this->fecha_modificacion)->toDateTimeString(),
            'fecha_modificacion_formatted' => \App\Helpers\DateHelper::format($this->fecha_modificacion),
        ];
    }
}
