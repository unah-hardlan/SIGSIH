<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrigenResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_origen_pk' => $this->id_origen_pk,
            'nombre_origen' => $this->nombre_origen,
            'descripcion_origen' => $this->descripcion_origen,
            'activo' => (bool) $this->activo,
        ];
    }
}
