<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngresosResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id_ingresos_pk' => $this->id_ingresos_pk,
            'nombre_ingreso' => $this->nombre_ingreso,
            'fecha_ingreso' => $this->fecha_ingreso,
            'monto_ingreso' => $this->monto_ingreso,
            'descripcion_ingreso' => $this->descripcion_ingreso,
            'id_proyecto_fk' => $this->id_proyecto_fk,
            'id_categoria_fk' => $this->id_categoria_fk,
            
            // Relaciones
            'proyecto' => $this->whenLoaded('proyecto', function () {
                return [
                    'id_proyecto_pk' => $this->proyecto->id_proyecto_pk,
                    'nombre_proyecto' => $this->proyecto->nombre_proyecto,
                    'descripcion_proyecto' => $this->proyecto->descripcion_proyecto,
                ];
            }),
            'categoria' => $this->whenLoaded('categoria', function () {
                return [
                    'id_categoria_pk' => $this->categoria->id_categoria_pk,
                    'nombre_categoria' => $this->categoria->nombre_categoria,
                ];
            }),
        ];
    }
}
