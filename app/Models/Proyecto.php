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
        'id_orden_servicio_fk',
        'id_estado_proyecto_fk'
    ];

    protected $casts = [
        'fecha_inicio_proyecto' => 'date',
        'fecha_estimada_fin_proyecto' => 'date', 
        'fecha_finalizacion_proyecto' => 'date',
    ];

    // Accessors para nombres simplificados (usando columnas virtuales)
    public function getFechaInicioAttribute()
    {
        return $this->fecha_inicio_proyecto;
    }

    public function getFechaEstimadaFinAttribute()
    {
        return $this->fecha_estimada_fin_proyecto;
    }

    public function getFechaFinAttribute()
    {
        return $this->fecha_finalizacion_proyecto;
    }

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

    /**
     * Relación con actividades del proyecto (plan estructurado)
     */
    public function actividades()
    {
        return $this->hasMany(ProyectoActividad::class, 'id_proyecto_fk', 'id_proyecto_pk');
    }

    /**
     * Relación con gastos del proyecto
     */
    public function gastos()
    {
        return $this->hasMany(Gastos::class, 'id_proyecto_fk', 'id_proyecto_pk');
    }

    /**
     * Relación con ingresos del proyecto
     */
    public function ingresos()
    {
        return $this->hasMany(Ingresos::class, 'id_proyecto_fk', 'id_proyecto_pk');
    }

    // Métodos de utilidad para actividades
    public function getActividadActual()
    {
        return $this->actividades()
                   ->whereIn('estado_actividad', ['EN_PROGRESO', 'PENDIENTE'])
                   ->orderBy('orden')
                   ->first();
    }

    public function getProgresoGeneralAttribute()
    {
        $actividades = $this->actividades;
        if ($actividades->count() === 0) return 0;

        $progresoTotal = $actividades->sum('progreso_porcentaje');
        return round($progresoTotal / $actividades->count(), 2);
    }

    public function isCompletado()
    {
        return $this->estadoProyecto && 
               in_array(strtoupper($this->estadoProyecto->nombre_estado), ['COMPLETADO', 'FINALIZADO', 'CERRADO']);
    }

    public function isEnProgreso()
    {
        return $this->estadoProyecto && 
               in_array(strtoupper($this->estadoProyecto->nombre_estado), ['EN_PROGRESO', 'ACTIVO', 'EJECUTANDO']);
    }

    public function getDuracionDiasAttribute()
    {
        if (!$this->fecha_inicio_proyecto) return null;
        
        $fechaFin = $this->fecha_finalizacion_proyecto ?? $this->fecha_estimada_fin_proyecto ?? now();
        return $this->fecha_inicio_proyecto->diffInDays($fechaFin);
    }
}
