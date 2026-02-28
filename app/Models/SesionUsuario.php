<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesionUsuario extends Model
{
    protected $table      = 'tbl_ms_sesiones';
    protected $primaryKey = 'id_sesion_pk';

    /** El PK es el hash SHA-256 del token (string de 32 chars), no auto-incremental */
    public $incrementing = false;
    protected $keyType   = 'string';

    /** Columnas de auditoría propias; no usar created_at / updated_at de Laravel */
    public $timestamps = false;

    protected $fillable = [
        'id_sesion_pk',
        'id_usuario_fk',
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
