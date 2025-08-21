<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCalificacionServicioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre_calificacion' => 'required|string|max:50|unique:tbl_calificacion_servicio,nombre_calificacion',
            'descripcion_calificacion' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_calificacion.required' => 'El nombre de la calificación es obligatorio.',
            'nombre_calificacion.unique' => 'Ya existe una calificación con este nombre.',
            'descripcion_calificacion.required' => 'La descripción es obligatoria.',
            'descripcion_calificacion.max' => 'La descripción no puede exceder los 255 caracteres.',
        ];
    }
}
