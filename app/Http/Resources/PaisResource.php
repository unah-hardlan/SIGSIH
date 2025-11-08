<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaisResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_pais_pk' => $this->id_pais_pk,
            'nombre_pais' => $this->nombre_pais
        ];
    }
}
