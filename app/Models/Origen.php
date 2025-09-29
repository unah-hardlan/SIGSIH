<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Origen extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_origen';
    protected $primaryKey = 'id_origen_pk';
    protected $fillable = [
        'nombre_origen',
        'descripcion_origen',
        'activo',
    ];
}
