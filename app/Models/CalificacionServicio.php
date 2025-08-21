<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalificacionServicio extends Model
{
    use HasFactory;
    
    public $timestamps = false;

    protected $table = 'tbl_calificacion_servicio';
    protected $primaryKey = 'id_calificacion_servicio_pk';
    
    protected $fillable = [
        'nombre_calificacion',
        'descripcion_calificacion',
    ];
}
