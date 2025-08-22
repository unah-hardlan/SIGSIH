<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoriaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_categoria_pk' => $this->id_categoria_pk,
            'tipo_categoria' => $this->tipo_categoria,
            'nombre_categoria' => $this->nombre_categoria,
        ];
    }
}
