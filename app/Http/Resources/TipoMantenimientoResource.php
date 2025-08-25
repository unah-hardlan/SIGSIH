<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoMantenimientoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_tipo_mantenimiento_pk' => $this->id_tipo_mantenimiento_pk,
            'tipo_mantenimiento' => $this->tipo_mantenimiento,
            'descripcion_mantenimiento' => $this->descripcion_mantenimiento,
        ];
    }
}
