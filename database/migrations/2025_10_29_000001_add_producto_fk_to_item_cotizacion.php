<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('tbl_item_cotizacion', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_item_cotizacion', 'id_producto_fk')) {
                // Use signed integer to match existing `tbl_producto.id_producto_pk` (signed in current schema)
                $table->integer('id_producto_fk')->nullable()->after('id_item_cotizacion_pk');
                $table->foreign('id_producto_fk')
                    ->references('id_producto_pk')->on('tbl_producto')
                    ->onUpdate('cascade')->onDelete('set null');
            }
        });
    }

    public function down()
    {
        Schema::table('tbl_item_cotizacion', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_item_cotizacion', 'id_producto_fk')) {
                $table->dropForeign(['id_producto_fk']);
                $table->dropColumn('id_producto_fk');
            }
        });
    }
};
