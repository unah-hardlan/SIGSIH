<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_cotizacion';
    protected $primaryKey = 'id_cotizacion_pk';

    protected $fillable = [
        'fecha_cotizacion',
        'valido_hasta',
        'subtotal',
        'total',
        'imponible',
        'impuesto',
        'total_impuesto',
        'otros_cargos',
        'impuesto_otros',
        'anticipo_requerido',
        'id_estado_cotizacion_fk',
        'id_cliente_fk',
        'id_orden_servicio_fk',
        'es_activo',
    ];

    protected $casts = [
        'fecha_cotizacion' => 'datetime',
        'valido_hasta' => 'date',
        'subtotal' => 'float',
        'total' => 'float',
        'imponible' => 'float',
        'impuesto' => 'float',
        'total_impuesto' => 'float',
        'otros_cargos' => 'float',
        'impuesto_otros' => 'float',
        'anticipo_requerido' => 'float',
        'es_activo' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            // Keep valido_hasta optional at form level, but ensure a sensible default
            // when DB constraints require a non-null value.
            if (!$model->valido_hasta) {
                try {
                    $base = $model->fecha_cotizacion ? \Carbon\Carbon::parse($model->fecha_cotizacion) : now();
                    $model->valido_hasta = $base->copy()->addDays(30)->toDateString();
                } catch (\Throwable $e) {
                    $model->valido_hasta = now()->addDays(30)->toDateString();
                }
            }

            if ($model->es_activo === null) {
                $model->es_activo = true;
            }

            try {
                if (!$model->id_estado_cotizacion_fk) {
                    $estadoId = EstadoCotizacion::where('codigo', 'borrador')->value('id_estado_cotizacion_pk');
                    if ($estadoId) {
                        $model->id_estado_cotizacion_fk = $estadoId;
                    }
                }
            } catch (\Throwable $e) {
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'id_orden_servicio_fk', 'id_orden_servicio_pk');
    }

    public function estado()
    {
        return $this->belongsTo(EstadoCotizacion::class, 'id_estado_cotizacion_fk', 'id_estado_cotizacion_pk');
    }
}
