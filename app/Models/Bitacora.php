<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bitacora extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_ms_bitacora';
    protected $primaryKey = 'id_bitacora_pk';
    protected $fillable = [
        'fecha_evento',
        'id_usuario_fk',
        'id_objetos_fk',
        'accion',
        'descripcion',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_fk');
    }

    public function objeto()
    {
        return $this->belongsTo(Objeto::class, 'id_objetos_fk');
    }
}
