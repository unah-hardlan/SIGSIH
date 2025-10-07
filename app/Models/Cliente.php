<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'tbl_cliente';
    protected $primaryKey = 'id_cliente_pk';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_cliente_pk',
        'id_persona_fk',
        'id_empresa_cliente_fk',
        'tipo_cliente',
        'fecha_registro',
        'estado_cliente'
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
    ];

    /**
     * Relación con EmpresaCliente (uno a uno)
     */
    public function empresa()
    {
        return $this->hasOne(EmpresaCliente::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    /**
     * Relación con Persona (muchos a uno)
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class, 'id_persona_fk', 'id_persona_pk');
    }

    /**
     * Relación con contactos
     */
    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    /**
     * Scope para clientes empresas
     */
    public function scopeEmpresa($query)
    {
        return $query->whereNotNull('id_empresa_cliente_fk');
    }

    /**
     * Scope para clientes personas
     */
    public function scopePersona($query)
    {
        return $query->whereNotNull('id_persona_fk');
    }
}