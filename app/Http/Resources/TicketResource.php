<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        $clienteData = null;
        if ($this->relationLoaded('cliente') && $this->cliente) {
            $cli = $this->cliente;
            
            $nombre = null;
            try {
                if ($cli->relationLoaded('empresa') && $cli->empresa) {
                    $nombre = $cli->empresa->nombre_comercial ?: $cli->empresa->razon_social;
                }
            } catch (\Throwable $e) {  }
            $clienteData = array_filter([
                'id_cliente_pk' => $cli->id_cliente_pk ?? null,
                'tipo_cliente' => $cli->tipo_cliente ?? null,
                'nombre' => $nombre,
            ], fn($v)=>$v!==null);
        }

        return [
            'id_ticket_pk' => $this->id_ticket_pk,
            'fecha_creacion' => $this->fecha_creacion?->format('Y-m-d H:i:s'),
            'descripcion_ticket' => $this->descripcion_ticket,
            'id_estado_ticket_fk' => $this->id_estado_ticket_fk,
            'id_tecnico_fk' => $this->id_tecnico_fk,
            'id_cliente_fk' => $this->id_cliente_fk,
            
            
            'estado' => $this->whenLoaded('estado'),
            'tecnico' => $this->whenLoaded('tecnico'),
            'cliente' => $this->whenLoaded('cliente', fn() => $clienteData),
        ];
    }
}
