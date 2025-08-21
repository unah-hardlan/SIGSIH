<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneroResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id_genero_pk,
            'genero' => $this->genero,
        ];
    }
}
