<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Origen extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tbl_origen';
    protected $primaryKey = 'id_origen_pk';
    public $incrementing = false;
    protected $keyType = 'int';
    protected $fillable = [
        'nombre_origen',
        'descripcion_origen',
        'activo',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function($model){
            if (empty($model->id_origen_pk)) {
                $next = (int) (static::max('id_origen_pk') ?? 0) + 1;
                $model->id_origen_pk = $next;
            }
            if (!isset($model->activo)) {
                $model->activo = 1;
            }
        });
    }
}
