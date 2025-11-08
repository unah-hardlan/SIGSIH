<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PerfilResource extends JsonResource
{
    
    public function toArray($request): array
    {
        return [
            'id' => $this->id_perfil_pk,
            'nombre_perfil' => $this->nombre_perfil,
            'descripcion_perfil' => $this->descripcion_perfil,
        ];
    }
}
