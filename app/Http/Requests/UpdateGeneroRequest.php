<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGeneroRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    
    public function rules(): array
    {
        $id = $this->route('genero') ?? $this->route('id');
        return [
            'genero' => 'sometimes|required|string|max:50|unique:tbl_genero,genero,' . $id . ',id_genero_pk',
        ];
    }
}