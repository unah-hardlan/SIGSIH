<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoOrdenServicio extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_orden_servicio';
    protected $primaryKey = 'id_estado_orden_servicio_pk';
    public $timestamps = false;

    protected $fillable = [
        'codigo','nombre','descripcion','es_final','orden'
    ];
}
