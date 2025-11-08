<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaCliente extends Model
{
    use HasFactory;

    protected $table = 'tbl_cliente_empresa';
    protected $primaryKey = 'id_cliente_fk';
    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_cliente_fk',
        'nombre_comercial',
        'razon_social',
        'rtn',
        'descripcion_empresa',
        'horario_atencion'
    ];

    
    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente_fk', 'id_cliente_pk');
    }

    public function contactos()
    {
        return $this->hasMany(Contacto::class, 'id_cliente_fk', 'id_cliente_fk');
    }
}
