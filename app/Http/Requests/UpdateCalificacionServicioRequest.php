<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCalificacionServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('calificacion_servicio') ?? $this->route('id');
        
        return [
            'nombre_calificacion' => "sometimes|string|max:50|unique:tbl_calificacion_servicio,nombre_calificacion,{$id},id_calificacion_servicio_pk",
            'descripcion_calificacion' => 'sometimes|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_calificacion.unique' => 'Ya existe una calificación con este nombre.',
            'descripcion_calificacion.max' => 'La descripción no puede exceder los 255 caracteres.',
        ];
    }
}
