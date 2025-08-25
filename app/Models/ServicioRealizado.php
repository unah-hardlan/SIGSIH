<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicioRealizado extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_servicio_realizado';
    protected $primaryKey = 'id_servicio_realizado_pk';

    protected $fillable = [
        'nombre_servicio',
        'descripcion_servicio',
    ];
}
