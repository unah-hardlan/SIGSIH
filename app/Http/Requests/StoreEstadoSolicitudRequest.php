<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEstadoSolicitudRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100|unique:tbl_estado_solicitud,nombre',
            'descripcion' => 'nullable|string|max:255',
            'codigo' => 'nullable|string|max:50',
            'es_final' => 'nullable|boolean',
            'orden' => 'nullable|integer|min:0'
        ];
    }
}
