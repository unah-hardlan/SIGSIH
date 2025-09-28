<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProyectoActividad extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_proyecto_actividad';
    protected $primaryKey = 'id_proyecto_actividad_pk';

    protected $fillable = [
        'id_proyecto_fk',
        'nombre_actividad',
        'descripcion_actividad',
        'fecha_inicio_actividad',
        'fecha_fin_actividad',
        'estado_actividad',
        'orden',
        'id_responsable_fk',
        'progreso_porcentaje',
        'observaciones',
    ];

    protected $casts = [
        'fecha_inicio_actividad' => 'date',
        'fecha_fin_actividad' => 'date',
        'fecha_creacion' => 'datetime',
        'progreso_porcentaje' => 'decimal:2',
        'orden' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        // Auto-asignar orden si no se especifica
        static::creating(function($model){
            if(!$model->orden) {
                $maxOrden = static::where('id_proyecto_fk', $model->id_proyecto_fk)->max('orden');
                $model->orden = ($maxOrden ?? 0) + 1;
            }
            if(!$model->fecha_creacion) {
                $model->fecha_creacion = now();
            }
        });
    }

    // Relaciones
    public function proyecto()
    {
        return $this->belongsTo(Proyecto::class, 'id_proyecto_fk', 'id_proyecto_pk');
    }

    public function responsable()
    {
        return $this->belongsTo(Persona::class, 'id_responsable_fk', 'id_persona_pk');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado_actividad', 'PENDIENTE');
    }

    public function scopeEnProgreso($query)
    {
        return $query->where('estado_actividad', 'EN_PROGRESO');
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado_actividad', 'COMPLETADA');
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }

    // Métodos de utilidad
    public function marcarCompletada()
    {
        $this->update([
            'estado_actividad' => 'COMPLETADA',
            'progreso_porcentaje' => 100.00
        ]);
    }

    public function actualizarProgreso($porcentaje)
    {
        $porcentaje = max(0, min(100, $porcentaje));
        
        $estado = 'PENDIENTE';
        if ($porcentaje > 0 && $porcentaje < 100) {
            $estado = 'EN_PROGRESO';
        } elseif ($porcentaje == 100) {
            $estado = 'COMPLETADA';
        }

        $this->update([
            'progreso_porcentaje' => $porcentaje,
            'estado_actividad' => $estado
        ]);
    }

    public function isCompletada()
    {
        return $this->estado_actividad === 'COMPLETADA';
    }

    public function isEnProgreso()
    {
        return $this->estado_actividad === 'EN_PROGRESO';
    }
}