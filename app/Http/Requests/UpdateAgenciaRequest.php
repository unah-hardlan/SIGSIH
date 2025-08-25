<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgenciaRequest extends FormRequest
{
    public function authorize(): bool 
    { 
        return true; 
    }

    public function rules(): array
    {
        $id = $this->route('agencia') ?? $this->route('id');
        return [
            'nombre_agencia' => 'sometimes|required|string|max:100|unique:tbl_agencias,nombre_agencia,' . $id . ',id_agencias_pk',
            'horario_agencia' => 'sometimes|required|string|max:50',
            'id_direccion_fk' => 'sometimes|required|exists:tbl_direccion,id_direccion_pk'
        ];
    }
}
