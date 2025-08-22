<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NombreEmpresa extends Model
{
    use HasFactory;

    protected $table = 'tbl_nombre_empresa';
    protected $primaryKey = 'id_nombre_empresa_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_empresa',
        'descripcion_empresa',
        'estado_empresa'
    ];
}
