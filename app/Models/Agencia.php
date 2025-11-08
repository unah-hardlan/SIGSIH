<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agencia extends Model
{
    use HasFactory;

    protected $table = 'tbl_agencias';
    protected $primaryKey = 'id_agencias_pk';
    public $timestamps = false;

    protected $fillable = [
        'nombre_agencia',
        'horario_agencia',
        'id_direccion_fk'
    ];

    
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion_fk', 'id_direccion_pk');
    }

    
    public function clientes()
    {
        return $this->belongsToMany(\App\Models\Cliente::class, 'tbl_agencia_cliente', 'id_agencia_fk', 'id_cliente_fk', 'id_agencias_pk', 'id_cliente_pk');
    }
}
