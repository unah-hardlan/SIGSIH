<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'tbl_servicio';
    protected $primaryKey = 'id_servicio_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_servicio',
        'tarifa'
    ];
}
