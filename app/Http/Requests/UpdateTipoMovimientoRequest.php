<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoMovimientoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('tipo_movimiento')
            ?? $this->route('tipos_movimiento')
            ?? $this->route('id')
            ?? $this->id_tipo_movimiento_pk
            ?? null;
        
        if(!$id){
            $segments = $this->segments();
            $last = end($segments);
            if(is_numeric($last)) { $id = (int) $last; }
        }
        return [
            'nombre_tipo_movimiento' => [
                'sometimes','string','max:50',
                Rule::unique('tbl_tipo_movimiento','nombre_tipo_movimiento')->ignore($id,'id_tipo_movimiento_pk')
            ],
            'descripcion_tipo_movimiento' => 'sometimes|nullable|string|max:255',
        ];
    }
}
