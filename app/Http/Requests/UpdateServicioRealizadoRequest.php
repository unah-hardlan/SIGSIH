<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateServicioRealizadoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('servicio_realizado')
            ?? $this->route('servicios_realizados')
            ?? $this->route('id')
            ?? $this->id_servicio_realizado_pk
            ?? null;
        if(!$id){
            $last = last($this->segments());
            if(is_numeric($last)) { $id = (int) $last; }
        }
        return [
            'nombre_servicio' => [
                'sometimes','string','max:100',
                Rule::unique('tbl_servicio_realizado','nombre_servicio')->ignore($id,'id_servicio_realizado_pk')
            ],
            'descripcion_servicio' => 'sometimes|nullable|string|max:255',
        ];
    }
}
