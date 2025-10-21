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
        'anticipo_requerido',
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
        'anticipo_requerido' => 'float',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if(!$model->fecha_cotizacion){
                $model->fecha_cotizacion = now();
            }
        });
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class,'id_cliente_fk','id_cliente_pk');
    }

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'id_orden_servicio_fk', 'id_orden_servicio_pk');
    }
}
