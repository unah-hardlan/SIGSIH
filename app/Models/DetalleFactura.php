<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleFactura extends Model
{
    protected $table = 'tbl_detalle_factura';
    protected $primaryKey = 'id_detalle_pk';
    public $timestamps = false;

    protected $fillable = [
        'id_factura_fk',
        'id_servicio_fk',
        'fecha_servicio',
        'horas',
        'descuento'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class, 'id_factura_fk', 'id_factura_pk');
    }

    public function servicio()
    {
        return $this->belongsTo(Servicio::class, 'id_servicio_fk', 'id_servicio_pk');
    }
}
