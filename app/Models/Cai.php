<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cai extends Model
{
    protected $table = 'tbl_cai';
    protected $primaryKey = 'id_cai_pk';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'rango_inicio',
        'rango_fin',
        'consecutivo_actual',
        'fecha_limite',
        'id_estado_cai_fk'
    ];

    public function estadoCai()
    {
        return $this->belongsTo(EstadoCai::class, 'id_estado_cai_fk', 'id_estado_cai_pk');
    }

    
    public function facturas()
    {
        return $this->hasMany(Factura::class, 'id_cai_fk', 'id_cai_pk');
    }

    
    public function isActivo()
    {
        return $this->estadoCai && $this->estadoCai->nombre_estado === 'ACTIVO' && 
               $this->fecha_limite >= now()->format('Y-m-d');
    }

    
    public function getProximoNumero()
    {
        return $this->consecutivo_actual + 1;
    }

    
    public function isNumeroValido($numero)
    {
        $num = is_numeric($numero) ? (int)$numero : (int)filter_var($numero, FILTER_SANITIZE_NUMBER_INT);
        return $num >= (int)$this->rango_inicio && $num <= (int)$this->rango_fin;
    }

    
    public function numerosDisponibles()
    {
        return (int)$this->rango_fin - $this->consecutivo_actual;
    }
}
