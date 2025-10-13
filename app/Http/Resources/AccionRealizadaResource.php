<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccionRealizadaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_accion_realizada_pk' => $this->id_accion_realizada_pk,
            'nombre' => $this->nombre_accion,
            'descripcion' => $this->descripcion_accion,
        ];
    }
}