<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::table('tbl_cliente_empresa', function (Blueprint $table) {
            $table->string('avatar', 255)->nullable()->after('horario_atencion');
        });
    }

    public function down() {
        Schema::table('tbl_cliente_empresa', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });
    }
};
