<?php

namespace App\Helpers;

use App\Models\Parametro;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DateHelper
{
    
    public static function format($value, ?string $format = null): string
    {
        if (empty($value)) return '';
        $fmt = $format ?? Cache::remember('appDateFormat', 300, function () {
            $v = optional(Parametro::where('parametro', 'APP.FORMATO_FECHA')->first())->valor
                ?? optional(Parametro::where('parametro', 'app.date_format')->first())->valor;
            return $v ?: 'Y-m-d';
        });
        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->format($fmt);
            }
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int)$value)->format($fmt);
            }
            return Carbon::parse($value)->format($fmt);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
