<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAccionRealizadaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('acciones_realizada')
            ?? $this->route('acciones-realizadas')
            ?? $this->route('id')
            ?? $this->id_accion_realizada_pk
            ?? null;
        if(!$id){
            $segments = $this->segments();
            $last = end($segments);
            if(is_numeric($last)) { $id = (int) $last; }
        }
        return [
            'nombre_accion' => [
                'sometimes','string','max:50',
                Rule::unique('tbl_accion_realizada','nombre_accion')->ignore($id,'id_accion_realizada_pk')
            ],
            'descripcion_accion' => 'sometimes|nullable|string|max:255',
        ];
    }
}
