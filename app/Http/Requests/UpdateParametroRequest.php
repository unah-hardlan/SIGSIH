<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParametroRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = $this->route('parametro');
        return [
            'parametro' => 'sometimes|required|string|max:100|unique:tbl_parametros,parametro,' . $id . ',id_parametro_pk',
            'valor' => 'sometimes|required|string|max:255',
            
            'id_usuario_fk' => 'nullable|integer|exists:tbl_ms_usuario,id_usuario_pk',
        ];
    }
}
