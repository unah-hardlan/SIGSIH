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
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->fecha_cotizacion) {
                $model->fecha_cotizacion = now();
            }
            // Default estado to 'borrador' if available
            try {
                if (!$model->id_estado_cotizacion_fk) {
                    $estadoId = EstadoCotizacion::where('codigo', 'borrador')->value('id_estado_cotizacion_pk');
                    if ($estadoId) {
                        $model->id_estado_cotizacion_fk = $estadoId;
                    }
                }
            } catch (\Throwable $e) {
                // ignore if table not yet migrated
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
