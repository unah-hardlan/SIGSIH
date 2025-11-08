<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CiudadResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_ciudad_pk' => $this->id_ciudad_pk,
            'nombre_ciudad' => $this->nombre_ciudad,
            'id_departamento_fk' => $this->id_departamento_fk,
            
            
            'departamento' => $this->whenLoaded('departamento')
        ];
    }
}
