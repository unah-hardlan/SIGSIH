<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Helper to get column type (INT/BIGINT) and unsigned of a referenced PK
        $getRefColumnType = function (string $table, string $column): ?array {
            $ref = DB::selectOne(
                "SELECT DATA_TYPE, COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?",
                [$table, $column]
            );
            if (!$ref) return null;
            $dataType = strtolower($ref->DATA_TYPE ?? 'int');
            $colType = strtolower($ref->COLUMN_TYPE ?? 'int');
            $type = str_contains($dataType, 'bigint') ? 'BIGINT' : 'INT';
            $unsigned = str_contains($colType, 'unsigned') ? ' UNSIGNED' : '';
            return [$type, $unsigned];
        };

        $fkExists = function (string $table, string $constraint): bool {
            $row = DB::selectOne(
                "SELECT 1 FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?",
                [$table, $constraint]
            );
            return (bool) $row;
        };

        $dropFkIfExists = function (string $table, string $constraint) use ($fkExists) {
            if ($fkExists($table, $constraint)) {
                DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
            }
        };

        $columnExists = function (string $table, string $column): bool {
            return Schema::hasColumn($table, $column);
        };

        // 1) Enforce pais -> departamento -> ciudad hierarchy with NOT NULL FKs
        if (Schema::hasTable('tbl_departamento') && Schema::hasTable('tbl_pais')) {
            if ($columnExists('tbl_departamento', 'id_pais_fk') && $columnExists('tbl_pais', 'id_pais_pk')) {
                $type = $getRefColumnType('tbl_pais', 'id_pais_pk') ?? ['INT', ''];
                [$t, $u] = $type;
                // Make FK column non-nullable and correct type
                DB::statement("ALTER TABLE `tbl_departamento` MODIFY `id_pais_fk` {$t}{$u} NOT NULL");
                // Ensure FK constraint
                $dropFkIfExists('tbl_departamento', 'fk_dep_pais');
                DB::statement("ALTER TABLE `tbl_departamento` ADD CONSTRAINT `fk_dep_pais` FOREIGN KEY (`id_pais_fk`) REFERENCES `tbl_pais`(`id_pais_pk`) ON UPDATE CASCADE ON DELETE RESTRICT");
            }
        }

        if (Schema::hasTable('tbl_ciudad') && Schema::hasTable('tbl_departamento')) {
            if ($columnExists('tbl_ciudad', 'id_departamento_fk') && $columnExists('tbl_departamento', 'id_departamento_pk')) {
                $type = $getRefColumnType('tbl_departamento', 'id_departamento_pk') ?? ['INT', ''];
                [$t, $u] = $type;
                DB::statement("ALTER TABLE `tbl_ciudad` MODIFY `id_departamento_fk` {$t}{$u} NOT NULL");
                $dropFkIfExists('tbl_ciudad', 'fk_ciu_dep');
                DB::statement("ALTER TABLE `tbl_ciudad` ADD CONSTRAINT `fk_ciu_dep` FOREIGN KEY (`id_departamento_fk`) REFERENCES `tbl_departamento`(`id_departamento_pk`) ON UPDATE CASCADE ON DELETE RESTRICT");
            }
        }

        if (Schema::hasTable('tbl_direccion') && Schema::hasTable('tbl_ciudad')) {
            if ($columnExists('tbl_direccion', 'id_ciudad_fk') && $columnExists('tbl_ciudad', 'id_ciudad_pk')) {
                $type = $getRefColumnType('tbl_ciudad', 'id_ciudad_pk') ?? ['INT', ''];
                [$t, $u] = $type;
                DB::statement("ALTER TABLE `tbl_direccion` MODIFY `id_ciudad_fk` {$t}{$u} NOT NULL");
                $dropFkIfExists('tbl_direccion', 'fk_dir_ciu');
                DB::statement("ALTER TABLE `tbl_direccion` ADD CONSTRAINT `fk_dir_ciu` FOREIGN KEY (`id_ciudad_fk`) REFERENCES `tbl_ciudad`(`id_ciudad_pk`) ON UPDATE CASCADE ON DELETE RESTRICT");
            }
        }

        // 2) Extend contacto: allow linking to persona or empresa and validate nullability for persona
        if (Schema::hasTable('tbl_contacto')) {
            // Ensure id_persona_fk is nullable to allow empresa-only contacts
            if ($columnExists('tbl_contacto', 'id_persona_fk') && Schema::hasTable('tbl_persona') && Schema::hasColumn('tbl_persona', 'id_persona_pk')) {
                $type = $getRefColumnType('tbl_persona', 'id_persona_pk') ?? ['INT', ''];
                [$t, $u] = $type;
                // Make nullable if currently NOT NULL
                // We can't introspect nullability easily without DBAL here; applying MODIFY to allow NULL is idempotent
                DB::statement("ALTER TABLE `tbl_contacto` MODIFY `id_persona_fk` {$t}{$u} NULL");
            }

            // Add id_empresa_cliente_fk when empresa table exists
            if (Schema::hasTable('tbl_empresa_cliente') && Schema::hasColumn('tbl_empresa_cliente', 'id_empresa_cliente_pk')) {
                if (!$columnExists('tbl_contacto', 'id_empresa_cliente_fk')) {
                    $type = $getRefColumnType('tbl_empresa_cliente', 'id_empresa_cliente_pk') ?? ['INT', ''];
                    [$t, $u] = $type;
                    DB::statement("ALTER TABLE `tbl_contacto` ADD `id_empresa_cliente_fk` {$t}{$u} NULL AFTER `id_persona_fk`");
                }
                // Add FK constraint if not present
                $exists = DB::selectOne(
                    "SELECT 1 FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_contacto' AND COLUMN_NAME = 'id_empresa_cliente_fk' AND REFERENCED_TABLE_NAME = 'tbl_empresa_cliente'"
                );
                if (!$exists) {
                    // Drop any leftover constraint name if exists
                    $fkName = 'fk_contacto_empresa';
                    if ($fkExists('tbl_contacto', $fkName)) {
                        DB::statement("ALTER TABLE `tbl_contacto` DROP FOREIGN KEY `{$fkName}`");
                    }
                    DB::statement("ALTER TABLE `tbl_contacto` ADD CONSTRAINT `{$fkName}` FOREIGN KEY (`id_empresa_cliente_fk`) REFERENCES `tbl_empresa_cliente`(`id_empresa_cliente_pk`) ON UPDATE CASCADE ON DELETE CASCADE");
                }
            }
        }
    }

    public function down(): void
    {
        // Best-effort rollback for contacto extension
        if (Schema::hasTable('tbl_contacto')) {
            // Drop FK then column for empresa
            $row = DB::selectOne("SELECT CONSTRAINT_NAME FROM information_schema.REFERENTIAL_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_contacto' AND CONSTRAINT_NAME = 'fk_contacto_empresa'");
            if ($row) {
                DB::statement("ALTER TABLE `tbl_contacto` DROP FOREIGN KEY `fk_contacto_empresa`");
            }
            if (Schema::hasColumn('tbl_contacto', 'id_empresa_cliente_fk')) {
                DB::statement("ALTER TABLE `tbl_contacto` DROP COLUMN `id_empresa_cliente_fk`");
            }

            // Optionally make id_persona_fk NOT NULL again (skip to avoid data loss)
        }

        // Optionally drop enforced FKs for geo hierarchy (skip destructive rollback)
    }
};
