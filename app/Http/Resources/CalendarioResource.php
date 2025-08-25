<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CalendarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_calendario_pk' => $this->id_calendario_pk,
            'fecha' => $this->fecha?->format('Y-m-d H:i:s'),
            'descripcion_calendario' => $this->descripcion_calendario,
            'observaciones_calendario' => $this->observaciones_calendario,
            'id_estado_calendario_fk' => $this->id_estado_calendario_fk,
            'id_agencias_fk' => $this->id_agencias_fk,
            'id_orden_servicio_fk' => $this->id_orden_servicio_fk,
            'id_tipo_mantenimiento_fk' => $this->id_tipo_mantenimiento_fk,
            'id_cliente_fk' => $this->id_cliente_fk,
            
            // Relaciones
            'estado' => $this->whenLoaded('estado'),
            'agencia' => $this->whenLoaded('agencia'),
            'orden_servicio' => $this->whenLoaded('ordenServicio'),
            'tipo_mantenimiento' => $this->whenLoaded('tipoMantenimiento'),
            'cliente' => $this->whenLoaded('cliente'),
        ];
    }
}
