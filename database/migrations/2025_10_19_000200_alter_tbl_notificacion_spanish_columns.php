<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tbl_notificacion')) return;

        // 1) Renames
        Schema::table('tbl_notificacion', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_notificacion', 'id')) {
                $table->renameColumn('id', 'id_notificacion');
            }
            if (Schema::hasColumn('tbl_notificacion', 'type')) {
                $table->renameColumn('type', 'tipo');
            }
            if (Schema::hasColumn('tbl_notificacion', 'notifiable_type')) {
                $table->renameColumn('notifiable_type', 'tipo_notificable');
            }
            if (Schema::hasColumn('tbl_notificacion', 'notifiable_id')) {
                $table->renameColumn('notifiable_id', 'id_notificable');
            }
            if (Schema::hasColumn('tbl_notificacion', 'read_at')) {
                $table->renameColumn('read_at', 'fecha_lectura');
            }
            if (Schema::hasColumn('tbl_notificacion', 'created_at')) {
                $table->renameColumn('created_at', 'fecha_creacion');
            }
            if (Schema::hasColumn('tbl_notificacion', 'updated_at')) {
                $table->renameColumn('updated_at', 'fecha_modificacion');
            }
        });

        // 2) New optional column without position constraints
        Schema::table('tbl_notificacion', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_notificacion', 'tipo_notificacion')) {
                $table->string('tipo_notificacion')->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_notificacion')) return;
        Schema::table('tbl_notificacion', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_notificacion', 'id_notificacion')) {
                $table->renameColumn('id_notificacion', 'id');
            }
            if (Schema::hasColumn('tbl_notificacion', 'tipo')) {
                $table->renameColumn('tipo', 'type');
            }
            if (Schema::hasColumn('tbl_notificacion', 'tipo_notificacion')) {
                $table->dropColumn('tipo_notificacion');
            }
            if (Schema::hasColumn('tbl_notificacion', 'tipo_notificable')) {
                $table->renameColumn('tipo_notificable', 'notifiable_type');
            }
            if (Schema::hasColumn('tbl_notificacion', 'id_notificable')) {
                $table->renameColumn('id_notificable', 'notifiable_id');
            }
            if (Schema::hasColumn('tbl_notificacion', 'fecha_lectura')) {
                $table->renameColumn('fecha_lectura', 'read_at');
            }
            if (Schema::hasColumn('tbl_notificacion', 'fecha_creacion')) {
                $table->renameColumn('fecha_creacion', 'created_at');
            }
            if (Schema::hasColumn('tbl_notificacion', 'fecha_modificacion')) {
                $table->renameColumn('fecha_modificacion', 'updated_at');
            }
        });
    }
};
