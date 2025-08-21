<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoProducto extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_tipo_producto';
    protected $primaryKey = 'id_tipo_producto_pk';
    protected $fillable = [
        'nombre_tipo_producto',
        'descripcion_tipo_producto',
    ];
}
