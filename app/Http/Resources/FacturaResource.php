<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FacturaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
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
            
            // Estado de factura usando patrón CAI
            'estado_factura' => $this->whenLoaded('estadoFactura', function () {
                return $this->estadoFactura->nombre ?? $this->estadoFactura->nombre_estado ?? 'Sin estado';
            }),
            
            // CAI usando patrón CAI
            'cai' => $this->whenLoaded('cai', function () {
                return $this->cai->codigo ?? 'Sin CAI';
            }),
            
            // Cliente con datos reales usando patrón CAI
            'cliente_nombre' => $this->whenLoaded('cliente', function () {
                if (!$this->cliente) return 'Sin cliente';
                
                // Si es cliente empresa
                if ($this->cliente->tipo_cliente === 'empresa' && $this->cliente->empresa) {
                    return $this->cliente->empresa->nombre_comercial ?? $this->cliente->empresa->razon_social ?? 'Empresa sin nombre';
                }
                
                // Si es cliente persona - obtener la primera persona de la colección
                if ($this->cliente->tipo_cliente === 'persona' && $this->cliente->personas) {
                    $persona = $this->cliente->personas->first();
                    if ($persona) {
                        $nombre = trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? ''));
                        return $nombre ?: 'Persona sin nombre';
                    }
                }
                
                return 'Cliente sin datos';
            }),
            
            // Datos adicionales para debugging
            'cliente_tipo' => $this->whenLoaded('cliente', function () {
                return $this->cliente->tipo_cliente ?? 'Sin tipo';
            }),
        ];
    }

    private function getClienteName($clienteId, $tipoCliente)
    {
        // Mapeo de IDs a nombres reales de clientes
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
