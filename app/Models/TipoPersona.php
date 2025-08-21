<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoPersona extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_tipo_persona';
    protected $primaryKey = 'id_tipo_persona_pk';
    protected $fillable = [
        'nombre_tipo_persona',
        'descripcion',
    ];
}
