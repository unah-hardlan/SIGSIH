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
        'referencia',
        'agencia_id'
    ];

    
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad_fk', 'id_ciudad_pk');
    }

    
    public function agencia()
    {
        return $this->belongsTo(Agencia::class, 'agencia_id', 'id_agencias_pk');
    }

    
    public function agencias()
    {
        return $this->hasMany(Agencia::class, 'id_direccion_fk', 'id_direccion_pk');
    }

    
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
