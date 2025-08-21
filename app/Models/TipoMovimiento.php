<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMovimiento extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_tipo_movimiento';
    protected $primaryKey = 'id_tipo_movimiento_pk';
    protected $fillable = [
        'nombre_tipo_movimiento',
        'descripcion_tipo_movimiento',
    ];
}
