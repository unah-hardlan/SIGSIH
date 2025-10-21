<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccionRealizada extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_accion_realizada';
    protected $primaryKey = 'id_accion_realizada_pk';

    protected $fillable = [
        'nombre_accion',
        'descripcion_accion',
    ];
}