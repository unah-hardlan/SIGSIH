<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;

class DbNotification extends BaseDatabaseNotification
{
    protected $table = 'tbl_notificacion';
    protected $primaryKey = 'id_notificacion';
    public $incrementing = false;
    protected $keyType = 'string';

    public const CREATED_AT = 'fecha_creacion';
    public const UPDATED_AT = 'fecha_modificacion';

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'fecha_lectura' => 'datetime',
        'fecha_creacion' => 'datetime',
        'fecha_modificacion' => 'datetime',
    ];

    // Keep Laravel's expected attribute names working in-app while persisting Spanish columns
    public function getIdAttribute()
    {
        return $this->attributes['id_notificacion'] ?? null;
    }

    public function setIdAttribute($value)
    {
        $this->attributes['id_notificacion'] = $value;
    }

    public function getTypeAttribute()
    {
        return $this->attributes['tipo'] ?? null;
    }

    public function setTypeAttribute($value)
    {
        $this->attributes['tipo'] = $value;
    }

    public function getReadAtAttribute()
    {
        return $this->attributes['fecha_lectura'] ?? null;
    }

    public function setReadAtAttribute($value)
    {
        $this->attributes['fecha_lectura'] = $value;
    }

    // Map default timestamp attributes to Spanish columns when payload includes them
    public function getCreatedAtAttribute()
    {
        return $this->attributes['fecha_creacion'] ?? null;
    }

    public function setCreatedAtAttribute($value)
    {
        $this->attributes['fecha_creacion'] = $value;
    }

    public function getUpdatedAtAttribute()
    {
        return $this->attributes['fecha_modificacion'] ?? null;
    }

    public function setUpdatedAtAttribute($value)
    {
        $this->attributes['fecha_modificacion'] = $value;
    }

    // Map default morph keys to Spanish columns for inserts from DatabaseChannel
    public function setNotifiableTypeAttribute($value)
    {
        $this->attributes['tipo_notificable'] = $value;
    }

    public function setNotifiableIdAttribute($value)
    {
        $this->attributes['id_notificable'] = $value;
    }

    // Polymorphic relation with custom keys
    public function notifiable()
    {
        return $this->morphTo(__FUNCTION__, 'tipo_notificable', 'id_notificable');
    }

    public function markAsRead()
    {
        $this->forceFill(['fecha_lectura' => $this->freshTimestamp()])->save();
    }
}
