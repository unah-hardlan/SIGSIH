<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenServicio extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_orden_servicio';
    protected $primaryKey = 'id_orden_servicio_pk';

    protected $fillable = [
        'id_solicitud_servicio_fk',
        'id_tecnico_fk',
        'fecha_recepcion',
        'fecha_inicio',
        'fecha_finalizacion',
        'observaciones',
        'diagnostico_tecnico',
        'diagnostico_cliente',
        'id_calificacion_servicio_fk',
        'id_cotizacion_fk'
    ];

    /**
     * Relación con el modelo Solicitud
     */
    public function solicitudServicio()
    {
        return $this->belongsTo(Solicitud::class, 'id_solicitud_servicio_fk', 'id_solicitud_pk');
    }

    /**
     * Relación con el modelo Persona (técnico)
     */
    public function tecnico()
    {
        return $this->belongsTo(Persona::class, 'id_tecnico_fk', 'id_persona_pk');
    }

    /**
     * Relación con el modelo CalificacionServicio
     */
    public function calificacionServicio()
    {
        return $this->belongsTo(CalificacionServicio::class, 'id_calificacion_servicio_fk', 'id_calificacion_servicio_pk');
    }

    /**
     * Relación con el modelo Cotización
     */
    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'id_cotizacion_fk', 'id_cotizacion_pk');
    }
}