<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'tbl_cliente';
    protected $primaryKey = 'id_cliente_pk';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
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
     * Relación con Persona a través de la tabla pivote tbl_cliente_persona.
     * Para clientes persona se espera un único registro asociado.
     */
    public function personas()
    {
        return $this->belongsToMany(
            Persona::class,
            'tbl_cliente_persona',
            'id_cliente_fk',
            'id_persona_fk'
        );
    }

    /**
     * Alias para mantener compatibilidad con código existente que invoca ->persona.
     */
    public function persona()
    {
        return $this->personas()->limit(1);
    }

    /**
     * Relación many-to-many con Agencias a través de la tabla pivote tbl_agencia_cliente
     */
    public function agencias()
    {
        return $this->belongsToMany(\App\Models\Agencia::class, 'tbl_agencia_cliente', 'id_cliente_fk', 'id_agencia_fk', 'id_cliente_pk', 'id_agencias_pk');
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
        return $query->where('tipo_cliente', 'empresa');
    }

    /**
     * Scope para clientes personas
     */
    public function scopePersona($query)
    {
        return $query->where('tipo_cliente', 'persona');
    }

    /**
     * Accessor para obtener el nombre del cliente
     */
    public function getNombreAttribute()
    {
        if ($this->tipo_cliente === 'empresa' && $this->relationLoaded('empresa') && $this->empresa) {
            return $this->empresa->nombre_comercial ?: $this->empresa->razon_social;
        }

        if ($this->tipo_cliente === 'persona' && $this->relationLoaded('personas')) {
            $persona = $this->personas->first();
            if ($persona) {
                return trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? ''));
            }
        }

        return 'N/A';
    }
}