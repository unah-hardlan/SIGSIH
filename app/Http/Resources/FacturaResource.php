<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'id_factura_pk' => $this->id_factura_pk,
            'numero' => $this->numero,
            'fecha' => $this->fecha,
            'oc' => $this->oc,
            'subtotal' => $this->subtotal,
            'impuesto' => $this->impuesto,
            'descuento' => $this->descuento,
            'total' => $this->total,
            'total_letras' => $this->total_letras,
            'id_estado_factura_fk' => $this->id_estado_factura_fk,
            'id_cai_fk' => $this->id_cai_fk,
            'id_cotizacion_fk' => $this->id_cotizacion_fk,
            'id_cliente_fk' => $this->id_cliente_fk,
            
            
            'estado_factura' => $this->whenLoaded('estadoFactura', function () {
                return $this->estadoFactura->nombre ?? $this->estadoFactura->nombre_estado ?? 'Sin estado';
            }),
            
            
            'cai' => $this->whenLoaded('cai', function () {
                return $this->cai->codigo ?? 'Sin CAI';
            }),
            
            
            'cliente_nombre' => $this->whenLoaded('cliente', function () {
                if (!$this->cliente) return 'Sin cliente';
                
                
                if ($this->cliente->tipo_cliente === 'empresa' && $this->cliente->empresa) {
                    return $this->cliente->empresa->nombre_comercial ?? $this->cliente->empresa->razon_social ?? 'Empresa sin nombre';
                }
                
                
                    if ($this->cliente->tipo_cliente === 'persona' && $this->cliente->persona) {
                        
                        $persona = $this->cliente->persona;
                        if ($persona instanceof \Illuminate\Database\Eloquent\Collection) {
                            $persona = $persona->first();
                        }
                        if ($persona) {
                            $nombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? ''));
                            return $nombre ?: 'Persona sin nombre';
                        }
                        return 'Persona sin nombre';
                    }
                
                return 'Cliente sin datos';
            }),
            
            
            'cliente_tipo' => $this->whenLoaded('cliente', function () {
                return $this->cliente->tipo_cliente ?? 'Sin tipo';
            }),
        ];
    }

    private function getClienteName($clienteId, $tipoCliente)
    {
        
        $clienteNames = [
            1 => 'BAC Credomatic',
            2 => 'Bancafe', 
            3 => 'Banco Atlántida',
            4 => 'BANPAIS',
            5 => 'Banco Popular'
        ];

        return $clienteNames[$clienteId] ?? "Cliente {$tipoCliente} #{$clienteId}";
    }
}
