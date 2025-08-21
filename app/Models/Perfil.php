<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perfil extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_perfil';
    protected $primaryKey = 'id_perfil_pk';
    protected $fillable = [
        'nombre_perfil',
        'descripcion_perfil',
    ];
}
