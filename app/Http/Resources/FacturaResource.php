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
            'id' => $this->id_factura_pk,
            'numero' => $this->numero,
            'fecha' => $this->fecha,
            'oc' => $this->oc,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'total_letras' => $this->total_letras,
            'estado_factura' => $this->estadoFactura ? [
                'id' => $this->estadoFactura->id_estado_factura_pk,
                'nombre_estado' => $this->estadoFactura->nombre_estado,
                'descripcion_estado_factura' => $this->estadoFactura->descripcion_estado_factura
            ] : null,
            'cai' => $this->cai ? [
                'id' => $this->cai->id_cai_pk,
                'codigo' => $this->cai->codigo
            ] : null,
            'cliente' => $this->cliente ? [
                'id' => $this->cliente->id_persona_pk,
                'nombre' => $this->cliente->nombre
            ] : null
        ];
    }
}
