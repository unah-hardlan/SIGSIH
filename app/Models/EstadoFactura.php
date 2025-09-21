<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoFactura extends Model
{
    protected $table = 'tbl_estado_factura';
    protected $primaryKey = 'id_estado_factura_pk';
    public $timestamps = false;

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'es_final', 'orden'
    ];
}
