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

    // Relación con Dirección (la agencia pertenece a una dirección)
    public function direccion()
    {
        return $this->belongsTo(Direccion::class, 'id_direccion_fk', 'id_direccion_pk');
    }

    /**
     * Relación many-to-many con Clientes a través de la tabla pivote tbl_agencia_cliente
     */
    public function clientes()
    {
        return $this->belongsToMany(\App\Models\Cliente::class, 'tbl_agencia_cliente', 'id_agencia_fk', 'id_cliente_fk', 'id_agencias_pk', 'id_cliente_pk');
    }
}
