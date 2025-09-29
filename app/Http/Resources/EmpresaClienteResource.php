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
            'id_empresa_cliente_pk' => $this->id_empresa_cliente_pk,
            'fecha_registro' => $this->fecha_registro,
            'id_nombre_empresa_fk' => $this->id_nombre_empresa_fk,
            'id_direccion_fk' => $this->id_direccion_fk,
            'id_oficina_fk' => $this->id_oficina_fk,
            'estado_empresa' => $this->estado_empresa,

            // Relaciones
            'nombre_empresa' => $this->whenLoaded('nombreEmpresa'),
            'direccion' => $this->whenLoaded('direccion'),
            'oficina' => $this->whenLoaded('oficina')
        ];
    }
}
