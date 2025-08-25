<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TipoVisitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_tipo_visita_pk' => $this->id_tipo_visita_pk,
            'nombre_tipo_visita' => $this->nombre_tipo_visita,
            'descripcion_tipo_visita' => $this->descripcion_tipo_visita,
        ];
    }
}
