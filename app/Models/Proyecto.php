<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proyecto extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tbl_proyectos';
    protected $primaryKey = 'id_proyecto_pk';

    protected $fillable = [
        'nombre_proyecto',
        'fecha_inicio_proyecto',
        'fecha_estimada_fin_proyecto',
        'fecha_finalizacion_proyecto',
        'descripcion_proyecto',
        'actividades_proyecto',
        'id_orden_servicio_fk',
        'id_estado_proyecto_fk'
    ];

    /**
     * Relación con el modelo OrdenServicio
     */
    public function ordenServicio()
    {
        return $this->belongsTo(OrdenServicio::class, 'id_orden_servicio_fk', 'id_orden_servicio_pk');
    }

    /**
     * Relación con el modelo EstadoProyecto
     */
    public function estadoProyecto()
    {
        return $this->belongsTo(EstadoProyecto::class, 'id_estado_proyecto_fk', 'id_estado_proyecto_pk');
    }
}
