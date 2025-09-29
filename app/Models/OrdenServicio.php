<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrdenServicio extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_orden_servicio';
    protected $primaryKey = 'id_orden_servicio_pk';

    protected $fillable = [
        'id_solicitud_servicio_fk',
        'id_tecnico_fk',
        'numero_orden_servicio',
        'fecha_creada',
        'fecha_asignada',
        'fecha_recepcion',
        'fecha_inicio',
        'fecha_finalizacion',
        'observaciones',
        'diagnostico_tecnico',
        'diagnostico_cliente',
        'id_calificacion_servicio_fk',
        'id_estado_orden_servicio_fk',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (self $model) {
            if (!$model->fecha_creada) {
                $model->fecha_creada = now();
            }
            if (!$model->id_estado_orden_servicio_fk) {
                $estadoId = EstadoOrdenServicio::where('codigo', 'creada')->value('id_estado_orden_servicio_pk');
                if ($estadoId) {
                    $model->id_estado_orden_servicio_fk = $estadoId;
                }
            }
        });

        static::created(function (self $model) {
            if (empty($model->numero_orden_servicio)) {
                $dt = $model->fecha_creada ? Carbon::parse($model->fecha_creada) : now();
                $numero = sprintf('OS-%s-%06d', $dt->format('Ym'), $model->id_orden_servicio_pk);
                $model->forceFill(['numero_orden_servicio' => $numero])->saveQuietly();
            }
        });
    }

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
        return $this->hasOne(Cotizacion::class, 'id_orden_servicio_fk', 'id_orden_servicio_pk');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoOrdenServicio::class, 'id_estado_orden_servicio_fk', 'id_estado_orden_servicio_pk');
    }
}