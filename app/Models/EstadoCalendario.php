<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoCalendario extends Model
{
    use HasFactory;

    protected $table = 'tbl_estado_calendario';
    protected $primaryKey = 'id_estado_calendario_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_estado',
        'descripcion_estado_calendario'
    ];
}
