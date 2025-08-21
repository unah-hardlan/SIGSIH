<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PersonaResource extends JsonResource
{
    /** @return array<string,mixed> */
    public function toArray($request): array
    {
        return [
            'id' => $this->id_persona_pk,
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
            'dni' => $this->dni,
            'cargo' => $this->cargo,
            'id_tipo_persona_fk' => $this->id_tipo_persona_fk,
            'id_genero_fk' => $this->id_genero_fk,
            'id_perfil_fk' => $this->id_perfil_fk,
            'id_usuario_fk' => $this->id_usuario_fk,
            'tipo_persona' => $this->whenLoaded('tipoPersona', fn() => [
                'id' => $this->tipoPersona->id_tipo_persona_pk,
                'nombre' => $this->tipoPersona->nombre_tipo_persona,
            ]),
            'genero' => $this->whenLoaded('genero', fn() => [
                'id' => $this->genero->id_genero_pk,
                'genero' => $this->genero->genero,
            ]),
            'perfil' => $this->whenLoaded('perfil', fn() => [
                'id' => $this->perfil->id_perfil_pk,
                'nombre' => $this->perfil->nombre_perfil,
            ]),
        ];
    }
}
