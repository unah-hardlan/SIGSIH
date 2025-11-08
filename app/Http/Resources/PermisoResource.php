<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermisoResource extends JsonResource
{
    
    public function toArray($request): array
    {
        return [
            'id' => $this->id_permiso_pk ?? null,
            'id_rol_fk' => $this->id_rol_fk,
            'id_objeto_fk' => $this->id_objeto_fk,
            'permiso_insercion' => (bool) $this->permiso_insercion,
            'permiso_consultar' => (bool) $this->permiso_consultar,
            'permiso_ver' => (bool) $this->permiso_ver,
            'permiso_actualizar' => (bool) $this->permiso_actualizar,
            'permiso_eliminacion' => (bool) $this->permiso_eliminacion,
            'creado_por' => $this->creado_por,
            'fecha_creacion' => optional($this->fecha_creacion)->toDateTimeString(),
            'modificado_por' => $this->modificado_por,
            'fecha_modificacion' => optional($this->fecha_modificacion)->toDateTimeString(),
            'rol' => $this->whenLoaded('rol', function () {
                return [
                    'id' => $this->rol->id_rol_pk,
                    'rol' => $this->rol->rol,
                ];
            }),
            'objeto' => $this->whenLoaded('objeto', function () {
                return [
                    'id' => $this->objeto->id_objetos_pk,
                    'nombre_objeto' => $this->objeto->nombre_objeto,
                ];
            }),
        ];
    }
}
