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
        'fecha_limite',
        'id_estado_cai_fk'
    ];

    public function estadoCai()
    {
        return $this->belongsTo(EstadoCai::class, 'id_estado_cai_fk', 'id_estado_cai_pk');
    }
}
