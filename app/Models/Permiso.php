<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_ms_permisos';
    protected $primaryKey = 'id_permiso_pk';
    protected $fillable = [
        'id_rol_fk',
        'id_objeto_fk',
        'permiso_insercion',
        'permiso_eliminacion',
        'permiso_actualizacion',
        'permiso_consultar',
    ];

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol_fk');
    }

    public function objeto()
    {
        return $this->belongsTo(Objeto::class, 'id_objeto_fk');
    }
}