<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConfigurarPerfilClienteRequest extends FormRequest
{
    
    public function authorize(): bool
    {
        return auth()->check();
    }

    
    public function rules(): array
    {
        return [
            'primer_nombre' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'segundo_nombre' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'primer_apellido' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'segundo_apellido' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/'
            ],
            'dni' => [
                'required',
                'string',
                'max:20',
                'unique:tbl_persona,dni,' . (\App\Models\Persona::where('id_usuario_fk', auth()->user()->id_usuario_pk ?? null)->value('id_persona_pk') ?? 'NULL') . ',id_persona_pk',
                'regex:/^[0-9A-Za-z-]+$/'
            ],
            'id_genero_fk' => [
                'required',
                'integer',
                'exists:tbl_genero,id_genero_pk'
            ],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048' // 2MB máximo
            ],
            'email_contacto' => [
                'required',
                'email',
                'max:255',
            ],
            'email_verificado' => [
                'nullable',
                'in:0,1'
            ],
        ];
    }

   
    public function messages(): array
    {
        return [
            'primer_nombre.required' => 'El primer nombre es obligatorio.',
            'primer_nombre.regex' => 'El primer nombre solo puede contener letras y espacios.',
            'primer_nombre.max' => 'El primer nombre no puede exceder los 100 caracteres.',
            
            'segundo_nombre.regex' => 'El segundo nombre solo puede contener letras y espacios.',
            'segundo_nombre.max' => 'El segundo nombre no puede exceder los 100 caracteres.',
            
            'primer_apellido.required' => 'El primer apellido es obligatorio.',
            'primer_apellido.regex' => 'El primer apellido solo puede contener letras y espacios.',
            'primer_apellido.max' => 'El primer apellido no puede exceder los 100 caracteres.',
            
            'segundo_apellido.regex' => 'El segundo apellido solo puede contener letras y espacios.',
            'segundo_apellido.max' => 'El segundo apellido no puede exceder los 100 caracteres.',
            
            'dni.required' => 'El DNI/documento de identidad es obligatorio.',
            'dni.unique' => 'Este DNI/documento ya está registrado en el sistema.',
            'dni.regex' => 'El DNI/documento solo puede contener números, letras y guiones.',
            'dni.max' => 'El DNI/documento no puede exceder los 20 caracteres.',
            
            'id_genero_fk.required' => 'Debe seleccionar un género.',
            'id_genero_fk.exists' => 'El género seleccionado no es válido.',
            
            'avatar.image' => 'El archivo debe ser una imagen.',
            'avatar.mimes' => 'La imagen debe ser de tipo: JPEG, PNG, JPG o WEBP (no se permiten GIF ni videos).',
            'avatar.max' => 'La imagen no puede ser mayor a 2MB.',

            'email_contacto.required' => 'El email de contacto es obligatorio.',
            'email_contacto.email' => 'El email de contacto no es válido.',
            'email_contacto.max' => 'El email de contacto no puede exceder 255 caracteres.',
        ];
    }

  
    public function attributes(): array
    {
        return [
            'primer_nombre' => 'primer nombre',
            'segundo_nombre' => 'segundo nombre',
            'primer_apellido' => 'primer apellido',
            'segundo_apellido' => 'segundo apellido',
            'dni' => 'DNI/documento',
            'id_genero_fk' => 'género',
            'avatar' => 'foto de perfil',
            'email_contacto' => 'email de contacto',
        ];
    }
}