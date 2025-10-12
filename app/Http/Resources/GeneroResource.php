<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneroResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id_genero_pk' => $this->id_genero_pk,
            'genero' => $this->genero,
        ];
    }
}