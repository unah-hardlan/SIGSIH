<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolicitudResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_solicitud_pk' => $this->id_solicitud_pk,
            'id_cliente_fk' => $this->id_cliente_fk,
            'numero_solicitud_acf' => $this->numero_solicitud_acf,
            'numero_solicitud_cliente' => $this->numero_solicitud_cliente,
            'descripcion_problema' => $this->descripcion_problema,
            'id_estado_solicitud_fk' => $this->id_estado_solicitud_fk,
            'id_contacto_fk' => $this->id_contacto_fk,
            
            // Relaciones
            'cliente' => $this->whenLoaded('cliente', function () {
                return [
                    'id' => $this->cliente->id,
                    'name' => $this->cliente->name,
                    'email' => $this->cliente->email,
                ];
            }),
            'estado_solicitud' => $this->whenLoaded('estadoSolicitud', function () {
                return [
                    'id_estado_solicitud_pk' => $this->estadoSolicitud->id_estado_solicitud_pk,
                    'nombre_estado' => $this->estadoSolicitud->nombre_estado,
                    'descripcion_estado' => $this->estadoSolicitud->descripcion_estado,
                ];
            }),
            'contacto' => $this->whenLoaded('contacto', function () {
                return [
                    'id_contacto_pk' => $this->contacto->id_contacto_pk,
                    'tipo_contacto' => $this->contacto->tipo_contacto,
                    'valor_contacto' => $this->contacto->valor_contacto,
                    'id_persona_fk' => $this->contacto->id_persona_fk,
                ];
            }),
        ];
    }
}
