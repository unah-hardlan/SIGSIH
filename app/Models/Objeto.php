<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Objeto extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_objetos';
    protected $primaryKey = 'id_objetos_pk';
    protected $fillable = [
        'objeto',
        'descripcion',
        'tipo_objeto',
    ];

    public function permisos()
    {
        return $this->hasMany(Permiso::class, 'id_objeto_fk');
    }

    public function bitacoras()
    {
        return $this->hasMany(Bitacora::class, 'id_objetos_fk');
    }
}
