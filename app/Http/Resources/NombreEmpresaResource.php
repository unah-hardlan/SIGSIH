<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NombreEmpresaResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_nombre_empresa_pk' => $this->id_nombre_empresa_pk,
            'nombre_empresa' => $this->nombre_empresa,
            'descripcion_empresa' => $this->descripcion_empresa,
            'estado_empresa' => $this->estado_empresa
        ];
    }
}
