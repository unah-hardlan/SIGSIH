<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialContrasena extends Model
{
    use HasFactory;

    protected $table = 'tbl_ms_hist_contrasena';
    protected $primaryKey = 'id_hist_pk';
    public $timestamps = false;

    protected $fillable = [
        'contrasena',
        'id_usuario_fk',
        'creado_por',
        'fecha_creacion',
        'modificado_por',
        'fecha_modificacion'
    ];

    protected $hidden = [
        'contrasena',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime'
    ];

    
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_fk', 'id_usuario_pk');
    }

    
    
}
