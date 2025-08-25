<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTipoVisitaRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('tipo_visita')
            ?? $this->route('tipos_visita')
            ?? $this->route('id')
            ?? $this->id_tipo_visita_pk
            ?? null;
        if(!$id){
            $last = last($this->segments());
            if(is_numeric($last)) { $id = (int) $last; }
        }
        return [
            'nombre_tipo_visita' => [
                'sometimes','string','max:50',
                Rule::unique('tbl_tipo_visita','nombre_tipo_visita')->ignore($id,'id_tipo_visita_pk')
            ],
            'descripcion_tipo_visita' => 'sometimes|nullable|string|max:255',
        ];
    }
}
