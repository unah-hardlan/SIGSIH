<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaCliente extends Model
{
    use HasFactory;

    protected $table = 'tbl_cliente_empresa';
    protected $primaryKey = 'id_cliente_fk';
    public $timestamps = false;

    protected $fillable = [
        'id_cliente_fk',
        'nombre_comercial',
        'razon_social',
        'rtn',
        'descripcion_empresa',
        'horario_atencion',
        'avatar'
    ];

    /**
     * Relación con Cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    // Relación con Oficina Empresa
    public function oficina()
    {
        return $this->belongsTo(OficinaEmpresa::class, 'id_oficina_fk', 'id_oficina_empresa_pk');
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'id_empresa_cliente_fk', 'id_empresa_cliente_pk');
    }
}
