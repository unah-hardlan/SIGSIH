<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteVisita extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_reportes_visita';
    protected $primaryKey = 'id_reportes_pk';

    protected $fillable = [
        'fecha_reporte',
        'observaciones',
        'id_tipo_visita_fk',
        'id_servicio_realizado_fk',
        'id_accion_realizada_fk',
        'id_orden_servicio_fk',
    ];

    protected $casts = [
        'fecha_reporte' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if(!$model->fecha_reporte){
                $model->fecha_reporte = now();
            }
        });
    }

    public function tipoVisita(){ return $this->belongsTo(TipoVisita::class,'id_tipo_visita_fk','id_tipo_visita_pk'); }
    public function servicioRealizado(){ return $this->belongsTo(ServicioRealizado::class,'id_servicio_realizado_fk','id_servicio_realizado_pk'); }
    public function accionRealizada(){ return $this->belongsTo(AccionRealizada::class,'id_accion_realizada_fk','id_accion_realizada_pk'); }
    public function ordenServicio(){ return $this->belongsTo('App\\Models\\OrdenServicio','id_orden_servicio_fk','id_orden_servicio_pk'); }
}
