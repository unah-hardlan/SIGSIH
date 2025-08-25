<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoVisita extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_tipo_visita';
    protected $primaryKey = 'id_tipo_visita_pk';

    protected $fillable = [
        'nombre_tipo_visita',
        'descripcion_tipo_visita',
    ];
}
