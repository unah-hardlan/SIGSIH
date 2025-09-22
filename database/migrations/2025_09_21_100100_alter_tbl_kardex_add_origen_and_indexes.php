<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_kardex', function (Blueprint $table) {
            // Add new columns only if they don't exist to be safe on existing databases
            if (!Schema::hasColumn('tbl_kardex', 'tipo_movimiento')) {
                $table->enum('tipo_movimiento', ['ENTRADA','SALIDA','AJUSTE'])->after('id_tipo_movimiento_fk');
            }
            if (!Schema::hasColumn('tbl_kardex', 'id_origen_fk')) {
                $table->unsignedBigInteger('id_origen_fk')->nullable()->after('tipo_movimiento');
            }
            if (!Schema::hasColumn('tbl_kardex', 'id_origen')) {
                $table->unsignedBigInteger('id_origen')->nullable()->after('id_origen_fk');
            }
        });

        // Composite index (id_producto_fk, fecha_movimiento) - create only if missing
        $idxName = 'idx_kardex_producto_fecha';
        $exists = DB::selectOne(
            "SELECT COUNT(1) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_kardex' AND INDEX_NAME = ?",
            [$idxName]
        );
        if (!$exists || (isset($exists->cnt) && (int)$exists->cnt === 0)) {
            DB::statement("ALTER TABLE tbl_kardex ADD INDEX {$idxName} (id_producto_fk, fecha_movimiento)");
        }

        // Add FK for id_origen_fk only if not present
        $fkName = 'tbl_kardex_id_origen_fk_foreign';
        $fkExists = DB::selectOne(
            "SELECT COUNT(1) as cnt FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_kardex' AND CONSTRAINT_NAME = ?",
            [$fkName]
        );
        if (!$fkExists || (isset($fkExists->cnt) && (int)$fkExists->cnt === 0)) {
            // Ensure column exists before adding FK
            if (Schema::hasColumn('tbl_kardex', 'id_origen_fk')) {
                DB::statement("ALTER TABLE tbl_kardex ADD CONSTRAINT {$fkName} FOREIGN KEY (id_origen_fk) REFERENCES tbl_origen(id_origen_pk)");
            }
        }
    }

    public function down(): void
    {
        // Drop only artifacts added by this migration
        // Drop FK if exists
        $fkName = 'tbl_kardex_id_origen_fk_foreign';
        $fkExists = DB::selectOne(
            "SELECT COUNT(1) as cnt FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_kardex' AND CONSTRAINT_NAME = ?",
            [$fkName]
        );
        if ($fkExists && isset($fkExists->cnt) && (int)$fkExists->cnt > 0) {
            DB::statement("ALTER TABLE tbl_kardex DROP FOREIGN KEY {$fkName}");
        }
        // Drop index if exists
        $idxName = 'idx_kardex_producto_fecha';
        $idxExists = DB::selectOne(
            "SELECT COUNT(1) as cnt FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_kardex' AND INDEX_NAME = ?",
            [$idxName]
        );
        if (!$idxExists || (isset($idxExists->cnt) && (int)$idxExists->cnt > 0)) {
            DB::statement("ALTER TABLE tbl_kardex DROP INDEX {$idxName}");
        }
        Schema::table('tbl_kardex', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_kardex', 'id_origen')) {
                $table->dropColumn('id_origen');
            }
            if (Schema::hasColumn('tbl_kardex', 'id_origen_fk')) {
                $table->dropColumn('id_origen_fk');
            }
            if (Schema::hasColumn('tbl_kardex', 'tipo_movimiento')) {
                $table->dropColumn('tipo_movimiento');
            }
        });
    }
};
