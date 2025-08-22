<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleOrdenProducto extends Model
{
    protected $table = 'tbl_detalle_orden_producto';
    protected $primaryKey = 'id_detalle_pk';
    public $timestamps = false;

    protected $fillable = [
        'id_orden_servicio_fk',
        'id_producto_fk',
        'cantidad'
    ];

    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'id_orden_servicio_fk', 'id_orden_servicio_pk');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto_fk', 'id_producto_pk');
    }
}
