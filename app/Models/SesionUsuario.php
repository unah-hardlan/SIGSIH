<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionUsuario extends Model
{
    protected $table      = 'tbl_ms_sesiones';
    protected $primaryKey = 'id_sesion_pk';

    /** PK entero autoincremental definido por la tabla legacy */
    public $incrementing = true;
    protected $keyType   = 'int';

    /** Columnas de auditoría propias; no usar created_at / updated_at de Laravel */
    public $timestamps = false;

    protected $fillable = [
        'id_usuario_fk',
        'token_refresh',
        'ip_direccion',
        'user_agent',
        'fecha_creacion',
        'fecha_expiracion',
        'activo',
    ];

    protected $casts = [
        'fecha_creacion'   => 'datetime',
        'fecha_expiracion' => 'datetime',
        'activo'           => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario_fk', 'id_usuario_pk');
    }
}
