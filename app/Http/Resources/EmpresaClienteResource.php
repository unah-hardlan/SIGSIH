<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpresaClienteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_cliente_fk' => $this->id_cliente_fk,
            'nombre_comercial' => $this->nombre_comercial,
            'razon_social' => $this->razon_social,
            'rtn' => $this->rtn,
            'descripcion_empresa' => $this->descripcion_empresa,
            'horario_atencion' => $this->horario_atencion,
            'fecha_registro' => $this->cliente?->fecha_registro?->toDateTimeString(),
            'estado_cliente' => $this->cliente?->estado_cliente,
            'tipo_cliente' => $this->cliente?->tipo_cliente,
            'contactos' => ContactoResource::collection($this->whenLoaded('contactos')),
        ];
    }
}
