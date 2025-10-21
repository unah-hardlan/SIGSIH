<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) Drop any existing unique index on numero_solicitud_cliente (regardless of its name)
        $dbName = DB::getDatabaseName();
        $indexes = DB::table('information_schema.statistics')
            ->select('INDEX_NAME')
            ->where('TABLE_SCHEMA', $dbName)
            ->where('TABLE_NAME', 'tbl_solicitud')
            ->where('COLUMN_NAME', 'numero_solicitud_cliente')
            ->groupBy('INDEX_NAME')
            ->get();

        foreach ($indexes as $idx) {
            try {
                DB::statement("ALTER TABLE `tbl_solicitud` DROP INDEX `{$idx->INDEX_NAME}`");
            } catch (\Throwable $e) {
                // ignore if drop fails
            }
        }

        // 2) Create composite unique index on (id_cliente_fk, numero_solicitud_cliente)
        try {
            DB::statement('ALTER TABLE `tbl_solicitud` ADD UNIQUE `tbl_solicitud_cliente_numero_unique` (`id_cliente_fk`, `numero_solicitud_cliente`)');
        } catch (\Throwable $e) {
            // ignore if already exists
        }
    }

    public function down(): void
    {
        // Drop composite unique index
        try {
            DB::statement('ALTER TABLE `tbl_solicitud` DROP INDEX `tbl_solicitud_cliente_numero_unique`');
        } catch (\Throwable $e) {
            // ignore
        }

        // Recreate unique index on numero_solicitud_cliente alone
        try {
            DB::statement('ALTER TABLE `tbl_solicitud` ADD UNIQUE `tbl_solicitud_numero_solicitud_cliente_unique` (`numero_solicitud_cliente`)');
        } catch (\Throwable $e) {
            // ignore
        }
    }
};
