<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'tbl_contacto';
    protected $primaryKey = 'id_contacto_pk';
    
    protected $fillable = [
        'tipo_contacto',
        'valor_contacto',
        'id_persona_fk',
        'id_empresa_cliente_fk',
    ];

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona_fk', 'id_persona_pk');
    }

    public function empresa()
    {
        return $this->belongsTo(EmpresaCliente::class, 'id_empresa_cliente_fk', 'id_empresa_cliente_pk');
    }

    // Simple format validation helper (call from services/requests)
    public static function isValidValor(string $tipo, string $valor): bool
    {
        $tipo = strtolower(trim($tipo));
        return match ($tipo) {
            'email' => filter_var($valor, FILTER_VALIDATE_EMAIL) !== false,
            'tel', 'telefono', 'phone' => (bool) preg_match('/^\+?[0-9 ()\-]{7,20}$/', $valor),
            'whatsapp', 'wa' => (bool) preg_match('/^\+?[0-9]{7,15}$/', $valor),
            default => strlen(trim($valor)) > 0,
        };
    }
}
