<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asiento extends Model
{
    use HasFactory;

    protected $table = 'tbl_asiento';
    protected $primaryKey = 'id_asiento_pk';
    public $timestamps = false;

    protected $fillable = [
        'fecha_asiento',
        'descripcion',
        'referencia',
        'estado',
    ];

    protected $casts = [
        'fecha_asiento' => 'datetime',
    ];

    public function detalles()
    {
        return $this->hasMany(AsientoDetalle::class, 'id_asiento_fk', 'id_asiento_pk');
    }
}
