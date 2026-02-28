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
            'codigo' => 'required|string|max:50|unique:tbl_estado_solicitud,codigo',
            'descripcion' => 'nullable|string|max:255',
            'es_final' => 'required|boolean',
            'orden' => 'required|integer',
        ];
    }
}