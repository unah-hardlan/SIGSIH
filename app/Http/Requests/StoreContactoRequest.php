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
            'id_cliente_fk' => ['required', 'integer', 'exists:tbl_cliente,id_cliente_pk'],
        ];
    }
}
 
