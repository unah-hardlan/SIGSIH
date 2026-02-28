<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
{
    
    public function toArray($request): array
    {
        return [
            'id' => $this->id_persona_pk,
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
            'dni' => $this->dni,
            'id_genero_fk' => $this->id_genero_fk,
            'id_usuario_fk' => $this->id_usuario_fk,
            'genero' => $this->whenLoaded('genero', fn() => [
                'id' => $this->genero->id_genero_pk,
                'genero' => $this->genero->genero,
            ]),
        ];
    }
}
