<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstadoProyecto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_estado_proyecto';
    protected $primaryKey = 'id_estado_proyecto_pk';

    protected $fillable = [
        'codigo', 'nombre', 'descripcion', 'es_final', 'orden'
    ];

    /**
     * Relación con proyectos (si existe tabla de proyectos)
     */
    // public function proyectos()
    // {
    //     return $this->hasMany(Proyecto::class, 'id_estado_proyecto_fk', 'id_estado_proyecto_pk');
    // }
}
