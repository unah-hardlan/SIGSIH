<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OficinaEmpresaResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_oficina_empresa_pk' => $this->id_oficina_empresa_pk,
            'nombre_oficina' => $this->nombre_oficina
        ];
    }
}
