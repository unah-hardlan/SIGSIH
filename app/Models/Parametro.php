<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Parametro extends Model
{
    use HasFactory;
    public $timestamps = false;

    // Tabla real en la base de datos (corregido de tbl_ms_parametros a tbl_parametros)
    protected $table = 'tbl_parametros';
    protected $primaryKey = 'id_parametro_pk';
    protected $fillable = [
        'parametro',
        'valor',
        'id_usuario_fk',
        'creado_por',
        'fecha_creacion',
        'modificado_por',
        'fecha_modificacion',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_fk');
    }
}
