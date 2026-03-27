<?php

namespace App\Http\Resources;

use App\Helpers\DateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

class BitacoraResource extends JsonResource
{

    public function toArray($request)
    {
        $usuario = $this->whenLoaded('usuario');
        $objeto = $this->whenLoaded('objeto');


        $creadoPor = $this->creado_por ?? ($usuario->usuario ?? null);
        $fechaCreacion = $this->fecha_creacion ?? $this->fecha_evento ?? null;

        return [
            'id' => $this->id_bitacora_pk ?? $this->id ?? null,
            'fecha_evento' => $this->fecha_evento,
            'fecha_evento_formatted' => DateHelper::format($this->fecha_evento),
            'accion' => $this->accion,
            'descripcion' => $this->descripcion,

            'id_usuario_fk' => $this->id_usuario_fk,
            'usuario' => $usuario ? [
                'id' => $usuario->id_usuario_pk ?? $usuario->id ?? null,
                'usuario' => $usuario->usuario ?? null,
                'nombre' => $usuario->nombre ?? null,
            ] : null,

            'id_objetos_fk' => $this->id_objetos_fk,
            'objeto' => $objeto ? [
                'id' => $objeto->id_objetos_pk ?? $objeto->id ?? null,
                'nombre_objeto' => $objeto->nombre_objeto ?? null,
            ] : null,

            'tabla' => $this->tabla,
            'id_registro' => $this->id_registro,
            'antes' => $this->antes,
            'despues' => $this->despues,
            'ip' => $this->ip,
            'user_agent' => $this->user_agent,


            'creado_por' => $creadoPor,
            'fecha_creacion' => $fechaCreacion,
            'fecha_creacion_formatted' => DateHelper::format($fechaCreacion),
        ];
    }
}
