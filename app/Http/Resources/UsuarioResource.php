<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id_usuario_pk,
            'usuario' => $this->usuario,
            'nombre_usuario' => $this->nombre_usuario,
            'estado_usuario' => $this->estado_usuario,
            'correo_electronico' => $this->correo_electronico,
            'id_rol_fk' => $this->id_rol_fk,
            'primer_ingreso' => (bool) $this->primer_ingreso,
            'fecha_ultima_conexion' => optional($this->fecha_ultima_conexion)->toDateTimeString(),
            'fecha_vencimiento' => optional($this->fecha_vencimiento)->toDateString(),
            'creado_por' => $this->creado_por,
            'fecha_creacion' => optional($this->fecha_creacion)->toDateTimeString(),
            'modificado_por' => $this->modificado_por,
            'fecha_modificacion' => optional($this->fecha_modificacion)->toDateTimeString(),
        ];
    }
}
