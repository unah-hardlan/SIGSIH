<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tbl_ms_bitacora', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_ms_bitacora', 'tabla')) {
                $table->string('tabla', 128)->nullable()->after('id_objetos_fk');
            }
            if (!Schema::hasColumn('tbl_ms_bitacora', 'id_registro')) {
                $table->unsignedBigInteger('id_registro')->nullable()->after('tabla');
            }
            if (!Schema::hasColumn('tbl_ms_bitacora', 'antes')) {
                $table->json('antes')->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('tbl_ms_bitacora', 'despues')) {
                $table->json('despues')->nullable()->after('antes');
            }
            if (!Schema::hasColumn('tbl_ms_bitacora', 'ip')) {
                $table->string('ip', 45)->nullable()->after('despues');
            }
            if (!Schema::hasColumn('tbl_ms_bitacora', 'user_agent')) {
                $table->text('user_agent')->nullable()->after('ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_ms_bitacora', function (Blueprint $table) {
            foreach (['tabla', 'id_registro', 'antes', 'despues', 'ip', 'user_agent'] as $col) {
                if (Schema::hasColumn('tbl_ms_bitacora', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};