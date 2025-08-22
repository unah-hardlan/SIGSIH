<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OficinaEmpresa extends Model
{
    use HasFactory;

    protected $table = 'tbl_oficina_empresa';
    protected $primaryKey = 'id_oficina_empresa_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_oficina'
    ];
}
