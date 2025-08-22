<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HistorialContrasenaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_hist_pk' => $this->id_hist_pk,
            'contrasena' => $this->contrasena, // Este campo estará oculto por el modelo
            'id_usuario_fk' => $this->id_usuario_fk,
            'creado_por' => $this->creado_por,
            'fecha_creacion' => $this->fecha_creacion?->format('Y-m-d H:i:s'),
            'modificado_por' => $this->modificado_por,
            'fecha_modificacion' => $this->fecha_modificacion?->format('Y-m-d H:i:s'),
            
            // Relaciones
            'usuario' => $this->whenLoaded('usuario'),
        ];
    }
}
