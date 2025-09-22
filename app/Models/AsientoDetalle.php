<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsientoDetalle extends Model
{
    use HasFactory;

    protected $table = 'tbl_asiento_detalle';
    protected $primaryKey = 'id_asiento_detalle_pk';
    public $timestamps = false;

    protected $fillable = [
        'id_asiento_fk',
        'cuenta',
        'descripcion',
        'debe',
        'haber',
    ];

    public function asiento()
    {
        return $this->belongsTo(Asiento::class, 'id_asiento_fk', 'id_asiento_pk');
    }
}
