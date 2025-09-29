<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmpresaClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_registro' => 'required|date',
            'id_nombre_empresa_fk' => 'required|exists:tbl_nombre_empresa,id_nombre_empresa_pk',
            'id_direccion_fk' => 'required|exists:tbl_direccion,id_direccion_pk',
            'id_oficina_fk' => 'required|exists:tbl_oficina_empresa,id_oficina_empresa_pk'
        ];
    }
}
