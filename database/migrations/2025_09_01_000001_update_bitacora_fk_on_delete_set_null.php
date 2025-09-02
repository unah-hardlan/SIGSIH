<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // MySQL requires dropping and re-adding the foreign key
        Schema::table('tbl_ms_bitacora', function (Blueprint $table) {
            // Use raw statements to be explicit about constraint name if needed
        });
    // Drop existing FK if present
    try { DB::statement('ALTER TABLE `tbl_ms_bitacora` DROP FOREIGN KEY `fk_bitacora_objetos`'); } catch (\Throwable $e) {}
    // Match foreign key type to referenced primary key
    $colTypeRow = DB::selectOne("SELECT COLUMN_TYPE AS ct FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_objetos' AND COLUMN_NAME = 'id_objetos_pk'");
    $colType = $colTypeRow?->ct ?? 'INT UNSIGNED';
    try { DB::statement("ALTER TABLE `tbl_ms_bitacora` MODIFY `id_objetos_fk` $colType NULL"); } catch (\Throwable $e) {}
    // Recreate FK with ON DELETE SET NULL
    DB::statement('ALTER TABLE `tbl_ms_bitacora` ADD CONSTRAINT `fk_bitacora_objetos` FOREIGN KEY (`id_objetos_fk`) REFERENCES `tbl_objetos`(`id_objetos_pk`) ON DELETE SET NULL ON UPDATE CASCADE');
    }

    public function down(): void
    {
        try { DB::statement('ALTER TABLE `tbl_ms_bitacora` DROP FOREIGN KEY `fk_bitacora_objetos`'); } catch (\Throwable $e) {}
    $colTypeRow = DB::selectOne("SELECT COLUMN_TYPE AS ct FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_objetos' AND COLUMN_NAME = 'id_objetos_pk'");
    $colType = $colTypeRow?->ct ?? 'INT UNSIGNED';
    try { DB::statement("ALTER TABLE `tbl_ms_bitacora` MODIFY `id_objetos_fk` $colType NOT NULL"); } catch (\Throwable $e) {}
        DB::statement('ALTER TABLE `tbl_ms_bitacora` ADD CONSTRAINT `fk_bitacora_objetos` FOREIGN KEY (`id_objetos_fk`) REFERENCES `tbl_objetos`(`id_objetos_pk`) ON DELETE RESTRICT ON UPDATE CASCADE');
    }
};
