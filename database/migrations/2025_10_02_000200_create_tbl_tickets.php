<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_tickets')) {
            Schema::create('tbl_tickets', function (Blueprint $table) {
                $table->increments('id_ticket_pk');
                $table->dateTime('fecha_creacion')->useCurrent();
                $table->string('descripcion_ticket', 500);
                $table->unsignedInteger('id_estado_ticket_fk');
                $table->unsignedInteger('id_tecnico_fk')->nullable();
                $table->unsignedInteger('id_cliente_fk')->nullable();
            });
        }

        // Add foreign keys only if referenced tables exist and the FK isn't already present
        if (Schema::hasTable('tbl_tickets')) {
            // Helper to check if constraint exists
            $fkExists = function(string $constraint) {
                $rows = DB::select("SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'tbl_tickets' AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = ?", [$constraint]);
                return !empty($rows);
            };

            if (Schema::hasTable('tbl_estado_ticket') && !$fkExists('fk_tickets_estado')) {
                try {
                    DB::statement("ALTER TABLE `tbl_tickets` ADD CONSTRAINT `fk_tickets_estado` FOREIGN KEY (`id_estado_ticket_fk`) REFERENCES `tbl_estado_ticket`(`id_estado_ticket_pk`) ON UPDATE CASCADE ON DELETE RESTRICT");
                } catch (\Throwable $e) {}
            }
            if (Schema::hasTable('tbl_persona')) {
                if (!$fkExists('fk_tickets_tecnico')) {
                    try {
                        DB::statement("ALTER TABLE `tbl_tickets` ADD CONSTRAINT `fk_tickets_tecnico` FOREIGN KEY (`id_tecnico_fk`) REFERENCES `tbl_persona`(`id_persona_pk`) ON UPDATE CASCADE ON DELETE RESTRICT");
                    } catch (\Throwable $e) {}
                }
                if (!$fkExists('fk_tickets_cliente')) {
                    try {
                        DB::statement("ALTER TABLE `tbl_tickets` ADD CONSTRAINT `fk_tickets_cliente` FOREIGN KEY (`id_cliente_fk`) REFERENCES `tbl_persona`(`id_persona_pk`) ON UPDATE CASCADE ON DELETE RESTRICT");
                    } catch (\Throwable $e) {}
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_tickets')) {
            // Drop FKs if exist by known names
            foreach (['fk_tickets_estado','fk_tickets_tecnico','fk_tickets_cliente'] as $fk) {
                try { DB::statement("ALTER TABLE `tbl_tickets` DROP FOREIGN KEY `{$fk}`"); } catch (\Throwable $e) {}
            }
            Schema::drop('tbl_tickets');
        }
    }
};
