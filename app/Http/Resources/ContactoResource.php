<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactoResource extends JsonResource
{
    
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id_contacto_pk,
            'tipo_contacto' => $this->tipo_contacto,
            'valor_contacto' => $this->valor_contacto,
            'id_cliente_fk' => $this->id_cliente_fk,
            'cliente' => $this->whenLoaded('cliente', function () {
                return $this->cliente ? [
                    'id_cliente_pk' => $this->cliente->id_cliente_pk,
                    'tipo_cliente' => $this->cliente->tipo_cliente,
                    'estado_cliente' => $this->cliente->estado_cliente,
                    'fecha_registro' => optional($this->cliente->fecha_registro)->toDateTimeString(),
                    'empresa' => $this->cliente->relationLoaded('empresa') && $this->cliente->empresa
                        ? [
                            'nombre_comercial' => $this->cliente->empresa->nombre_comercial,
                            'razon_social' => $this->cliente->empresa->razon_social,
                            'rtn' => $this->cliente->empresa->rtn,
                        ]
                        : null,
                ] : null;
            }),
        ];
    }
}
