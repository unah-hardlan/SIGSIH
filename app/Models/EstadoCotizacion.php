<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCotizacion extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_cotizacion';
    protected $primaryKey = 'id_estado_cotizacion_pk';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'es_final',
        'orden'
    ];
}
