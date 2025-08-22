<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    use HasFactory;

    protected $table = 'tbl_direccion';
    protected $primaryKey = 'id_direccion_pk';
    public $timestamps = false;

    protected $fillable = [
        'id_ciudad_fk'
    ];

    // Relación con Ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class, 'id_ciudad_fk', 'id_ciudad_pk');
    }
}
