<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParametroResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_parametro_pk,
            'parametro' => $this->parametro,
            'valor' => $this->valor,
            'usuario_id' => $this->id_usuario_fk,
            'creado_por' => $this->creado_por,
            'fecha_creacion' => $this->fecha_creacion ? (string) $this->fecha_creacion : null,
            'modificado_por' => $this->modificado_por,
            'fecha_modificacion' => $this->fecha_modificacion ? (string) $this->fecha_modificacion : null,
        ];
    }
}
