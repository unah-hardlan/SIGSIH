<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendario extends Model
{
    use HasFactory;

    protected $table = 'tbl_calendario';
    protected $primaryKey = 'id_calendario_pk';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'descripcion_calendario',
        'observaciones_calendario',
        'id_estado_calendario_fk',
        'id_agencias_fk',
        'id_usuario_fk',
        'id_orden_servicio_fk',
        'id_tipo_mantenimiento_fk',
        'id_cliente_fk'
    ];

    protected $casts = [
        'fecha' => 'datetime'
    ];

    /**
     * Relación con el estado del calendario
     */
    public function estado()
    {
        return $this->belongsTo(EstadoCalendario::class, 'id_estado_calendario_fk', 'id_estado_calendario_pk');
    }

    /**
     * Relación con la agencia
     */
    public function agencia()
    {
        return $this->belongsTo(Agencia::class, 'id_agencias_fk', 'id_agencias_pk');
    }

    /**
     * Relación con la orden de servicio
     */
    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'id_orden_servicio_fk', 'id_orden_servicio_pk');
    }

    /**
     * Relación con el tipo de mantenimiento
     */
    public function tipoMantenimiento()
    {
        return $this->belongsTo(TipoMantenimiento::class, 'id_tipo_mantenimiento_fk', 'id_tipo_mantenimiento_pk');
    }

    /**
     * Relación con el cliente
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    /**
     * Relación con el técnico (usuario)
     */
    public function tecnico()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_fk', 'id_usuario_pk');
    }
}
