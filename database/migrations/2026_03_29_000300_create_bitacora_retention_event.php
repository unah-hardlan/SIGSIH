<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        if (!Schema::hasTable('tbl_ms_bitacora')) {
            return;
        }

        // Requiere privilegios EVENT y event_scheduler habilitado en MySQL.
        DB::unprepared('DROP EVENT IF EXISTS ev_bitacora_retention_90d');
        DB::unprepared(
            'CREATE EVENT ev_bitacora_retention_90d
            ON SCHEDULE EVERY 1 DAY
            STARTS CURRENT_TIMESTAMP + INTERVAL 1 DAY
            DO
                DELETE FROM tbl_ms_bitacora
                WHERE fecha_creacion < (NOW() - INTERVAL 90 DAY)'
        );
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP EVENT IF EXISTS ev_bitacora_retention_90d');
    }
};