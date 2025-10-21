<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_categoria_pk' => $this->id_categoria_pk,
            'nombre_categoria' => $this->nombre_categoria,
            'descripcion_categoria' => $this->descripcion_categoria,
            'tipo_categoria' => $this->tipo_categoria,
        ];
    }
}