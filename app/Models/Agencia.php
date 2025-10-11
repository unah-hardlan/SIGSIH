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

    // Relación con Dirección
    public function direccion()
    {
        return $this->hasOne(Direccion::class, 'agencia_id', 'id_agencias_pk');
    }
}
