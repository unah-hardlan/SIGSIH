<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EstadoCai extends Model
{
    protected $table = 'tbl_estado_cai';
    protected $primaryKey = 'id_estado_cai_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_estado_cai',
        'descripcion_estado_cai'
    ];
}
