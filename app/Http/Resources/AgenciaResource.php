<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgenciaResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id_agencias_pk' => $this->id_agencias_pk,
            'nombre_agencia' => $this->nombre_agencia,
            'horario_agencia' => $this->horario_agencia,
            'id_direccion_fk' => $this->id_direccion_fk,
            
            
            'direccion' => $this->whenLoaded('direccion'),

            
            'clientes' => $this->whenLoaded('clientes', function () {
                return $this->clientes->map(function ($c) {
                    $nombre = '';
                    if (isset($c->tipo_cliente) && $c->tipo_cliente === 'empresa' && isset($c->empresa)) {
                        $nombre = $c->empresa->nombre_comercial ?? $c->empresa->razon_social ?? ('Cliente ' . $c->id_cliente_pk);
                    } else {
                        $p = $c->personas->first();
                        if ($p) {
                            $nombre = trim(($p->primer_nombre ?? '') . ' ' . ($p->segundo_nombre ?? '') . ' ' . ($p->primer_apellido ?? '') . ' ' . ($p->segundo_apellido ?? ''));
                        }
                        if (!$nombre) $nombre = 'Cliente ' . $c->id_cliente_pk;
                    }
                    return [ 'id' => $c->id_cliente_pk, 'nombre' => $nombre ];
                })->values();
            })
        ];
    }
}
