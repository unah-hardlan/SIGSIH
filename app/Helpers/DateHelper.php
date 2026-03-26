<?php

namespace App\Helpers;

use App\Models\Parametro;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DateHelper
{
   
    public const HONDURAS_TIMEZONE = 'America/Tegucigalpa';

    /**
     * 
     * 
     * @return Carbon
     */
    public static function now(): Carbon
    {
        return Carbon::now(self::HONDURAS_TIMEZONE);
    }

    /**
     * 
     * 
     * @param string|null $format
     * @return string
     */
    public static function nowFormatted(?string $format = null): string
    {
        $fmt = $format ?? self::getDefaultFormat();
        return self::now()->format($fmt);
    }

    /**
     * 
     * 
     * @return string
     */
    public static function getDefaultFormat(): string
    {
        return Cache::remember('appDateFormat', 300, function () {
            $v = optional(Parametro::where('parametro', 'APP.FORMATO_FECHA')->first())->valor
                ?? optional(Parametro::where('parametro', 'app.date_format')->first())->valor;
            return $v ?: 'Y-m-d';
        });
    }
    
    public static function format($value, ?string $format = null): string
    {
        if (empty($value)) return '';
        $fmt = $format ?? self::getDefaultFormat();
        try {
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->setTimezone(self::HONDURAS_TIMEZONE)->format($fmt);
            }
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int)$value, self::HONDURAS_TIMEZONE)->format($fmt);
            }
            return Carbon::parse($value, self::HONDURAS_TIMEZONE)->format($fmt);
        } catch (\Throwable $e) {
            return (string) $value;
        }
    }
}
