<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartamentoResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_departamento_pk' => $this->id_departamento_pk,
            'nombre_departamento' => $this->nombre_departamento,
            'id_pais_pk' => $this->id_pais_pk,
            
            
            'pais' => $this->whenLoaded('pais')
        ];
    }
}
