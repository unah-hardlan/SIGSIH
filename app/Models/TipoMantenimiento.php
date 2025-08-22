<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoMantenimiento extends Model
{
    use HasFactory;

    protected $table = 'tbl_tipo_mantenimiento';
    protected $primaryKey = 'id_tipo_mantenimiento_pk';
    public $timestamps = false;

    protected $fillable = [
        'tipo_mantenimiento',
        'descripcion_mantenimiento'
    ];
}
