<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo_contacto' => ['required', 'string', Rule::in(['email', 'tel', 'whatsapp'])],
            'valor_contacto' => ['required', function ($attribute, $value, $fail) {
                $tipo = strtolower((string) $this->input('tipo_contacto'));
                switch ($tipo) {
                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $fail('El formato de email no es válido.');
                        }
                        break;
                    case 'tel':
                        if (!preg_match('/^\+?[0-9 ()\-]{7,20}$/', (string) $value)) {
                            $fail('El teléfono debe contener 7-20 dígitos y opcionales +, -, espacios.');
                        }
                        break;
                    case 'whatsapp':
                        if (!preg_match('/^\+?[0-9]{7,15}$/', (string) $value)) {
                            $fail('WhatsApp debe ser numérico con 7-15 dígitos opcional +.');
                        }
                        break;
                    default:
                        $fail('Tipo de contacto no soportado.');
                }
            }],
            'id_persona_fk' => ['nullable', 'integer', 'exists:tbl_persona,id_persona_pk'],
            'id_empresa_cliente_fk' => ['nullable', 'integer', 'exists:tbl_empresa_cliente,id_empresa_cliente_pk'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($v) {
            $persona = $this->input('id_persona_fk');
            $empresa = $this->input('id_empresa_cliente_fk');
            if (empty($persona) && empty($empresa)) {
                $v->errors()->add('id_persona_fk', 'Debe asociar el contacto a una persona o a una empresa.');
            }
        });
    }
}
 
