<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_contacto_pk,
            'tipo_contacto' => $this->tipo_contacto,
            'valor_contacto' => $this->valor_contacto,
            'id_persona' => $this->id_persona_fk,
            'persona' => $this->whenLoaded('persona', function () {
                return $this->persona ? [
                    'id' => $this->persona->id_persona_pk,
                    'nombre' => $this->persona->nombre_persona ?? 'N/A',
                    'apellido' => $this->persona->apellido_persona ?? 'N/A',
                    'nombre_completo' => ($this->persona->nombre_persona ?? '') . ' ' . ($this->persona->apellido_persona ?? ''),
                ] : null;
            }),
        ];
    }
}
