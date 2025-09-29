<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'tbl_direccion';
    protected $primaryKey = 'id_direccion_pk';
    public $timestamps = false;

    protected $fillable = [
        'id_ciudad_fk',
        'calle',
        'numero',
        'colonia',
        'codigo_postal',
        'referencia'
    ];

    // Relación con Ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad_fk', 'id_ciudad_pk');
    }

    // Accessor para dirección completa
    public function getDireccionCompletaAttribute()
    {
        $direccion = $this->calle . ' ' . $this->numero . ', ' . $this->colonia;
        if ($this->codigo_postal) {
            $direccion .= ', CP ' . $this->codigo_postal;
        }
        if ($this->referencia) {
            $direccion .= ' (' . $this->referencia . ')';
        }
        return $direccion;
    }
}
