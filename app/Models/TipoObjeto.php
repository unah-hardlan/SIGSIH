<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoObjeto extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_tipo_objetos';
    protected $primaryKey = 'id_tipo_objeto_pk';
    protected $fillable = [
        'nombre_tipo_objeto',
        'descripcion_tipo_objeto',
    ];
}
