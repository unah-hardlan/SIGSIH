<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ObjetoResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id_objetos_pk,
            'nombre_objeto' => $this->nombre_objeto,
            'descripcion_objeto' => $this->descripcion_objeto,
            'id_tipo_objetos_fk' => $this->id_tipo_objetos_fk,
        'tipo' => $this->whenLoaded('tipoObjeto', function(){
                return [
            'id' => optional($this->tipoObjeto)->id_tipo_objeto_pk,
                    'nombre' => optional($this->tipoObjeto)->nombre_tipo_objeto,
                    'descripcion' => optional($this->tipoObjeto)->descripcion_tipo_objeto,
                ];
            }),
            'creado_por' => $this->creado_por,
            'fecha_creacion' => optional($this->fecha_creacion)->toDateTimeString(),
            'modificado_por' => $this->modificado_por,
            'fecha_modificacion' => optional($this->fecha_modificacion)->toDateTimeString(),
        ];
    }
}
