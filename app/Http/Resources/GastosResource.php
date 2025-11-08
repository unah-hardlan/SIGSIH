<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GastosResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_gasto_pk' => $this->id_gasto_pk,
            'nombre' => $this->nombre_gasto,
            'fecha' => $this->fecha_gasto,
            'monto' => $this->monto_gasto,
            'descripcion' => $this->descripcion_gasto,
            'id_proyecto_fk' => $this->id_proyecto_fk,
            'id_categoria_fk' => $this->id_categoria_fk,

            
            'proyecto' => $this->whenLoaded('proyecto'),
            'categoria' => $this->whenLoaded('categoria')
        ];
    }
}
