<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreProyectoRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre_proyecto' => 'required|string|max:100',
            'descripcion_proyecto' => 'nullable|string|max:500',
            'fecha_inicio_proyecto' => 'required|date|date_format:Y-m-d',
            'fecha_estimada_fin_proyecto' => 'nullable|date|date_format:Y-m-d|after_or_equal:fecha_inicio_proyecto',
            'fecha_finalizacion_proyecto' => 'nullable|date|date_format:Y-m-d|after_or_equal:fecha_inicio_proyecto',
            'id_orden_servicio_fk' => 'required|integer|exists:tbl_orden_servicio,id_orden_servicio_pk',
            'id_estado_proyecto_fk' => 'required|integer|exists:tbl_estado_proyecto,id_estado_proyecto_pk'
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_inicio_proyecto.date_format' => 'La fecha de inicio debe tener formato YYYY-MM-DD',
            'fecha_estimada_fin_proyecto.date_format' => 'La fecha fin estimada debe tener formato YYYY-MM-DD',
            'fecha_estimada_fin_proyecto.after_or_equal' => 'La fecha fin estimada debe ser posterior o igual a la fecha de inicio',
            'fecha_finalizacion_proyecto.date_format' => 'La fecha fin real debe tener formato YYYY-MM-DD',
            'fecha_finalizacion_proyecto.after_or_equal' => 'La fecha fin real debe ser posterior o igual a la fecha de inicio',
        ];
    }

    protected function passedValidation()
    {
        // Validar año fuera de rango (1900-2100)
        $start = $this->input('fecha_inicio_proyecto');
        $estFin = $this->input('fecha_estimada_fin_proyecto');
        $realFin = $this->input('fecha_finalizacion_proyecto');

        if ($start) {
            $y = (int)substr($start, 0, 4);
            if ($y < 1900 || $y > 2100) {
                $this->validator->errors()->add('fecha_inicio_proyecto', 'El año debe estar en el rango permitido (1900-2100)');
            }
        }
        if ($estFin) {
            $y = (int)substr($estFin, 0, 4);
            if ($y < 1900 || $y > 2100) {
                $this->validator->errors()->add('fecha_estimada_fin_proyecto', 'El año debe estar en el rango permitido (1900-2100)');
            }
        }
        if ($realFin) {
            $y = (int)substr($realFin, 0, 4);
            if ($y < 1900 || $y > 2100) {
                $this->validator->errors()->add('fecha_finalizacion_proyecto', 'El año debe estar en el rango permitido (1900-2100)');
            }
        }
    }
}
