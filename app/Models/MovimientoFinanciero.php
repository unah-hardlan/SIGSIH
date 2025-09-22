<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MovimientoFinanciero extends Model
{
    use HasFactory;

    protected $table = 'tbl_movimiento_financiero';
    protected $primaryKey = 'id_movimiento_financiero_pk';
    public $timestamps = false;

    protected $fillable = [
        'tipo_movimiento',
        'fecha_movimiento',
        'monto',
        'descripcion',
        'id_categoria_fk',
        'atribuible_a_proyecto',
        'id_orden_servicio_fk',
        'id_asiento_fk',
    ];

    protected $casts = [
        'fecha_movimiento' => 'datetime',
        'monto' => 'decimal:2',
        'atribuible_a_proyecto' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'id_categoria_fk', 'id_categoria_pk');
    }
    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'id_orden_servicio_fk', 'id_orden_servicio_pk');
    }
    public function asiento()
    {
        return $this->belongsTo(Asiento::class, 'id_asiento_fk', 'id_asiento_pk');
    }
}
