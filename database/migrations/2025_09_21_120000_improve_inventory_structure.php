<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Agregar SKU único a productos
        Schema::table('tbl_producto', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_producto', 'sku')) {
                $table->string('sku', 50)->unique()->after('id_producto_pk');
            }
            if (!Schema::hasColumn('tbl_producto', 'precio_costo')) {
                $table->decimal('precio_costo', 10, 2)->nullable()->after('precio_unitario');
            }
        });

        // 2. Limpiar duplicación en kardex
        if (Schema::hasColumn('tbl_kardex', 'tipo_movimiento_enum')) {
            // Migrar datos del campo duplicado si existe diferencia
            DB::statement("UPDATE tbl_kardex SET tipo_movimiento = tipo_movimiento_enum WHERE tipo_movimiento IS NULL OR tipo_movimiento = ''");
            
            Schema::table('tbl_kardex', function (Blueprint $table) {
                $table->dropColumn('tipo_movimiento_enum');
            });
        }

        // 3. Limpiar campo 'origen' varchar duplicado (usar id_origen_fk mejor)
        if (Schema::hasColumn('tbl_kardex', 'origen') && Schema::hasColumn('tbl_kardex', 'id_origen_fk')) {
            Schema::table('tbl_kardex', function (Blueprint $table) {
                $table->dropColumn('origen'); // Preferir id_origen_fk que ya está implementado
            });
        }
    }

    public function down(): void
    {
        Schema::table('tbl_producto', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_producto', 'precio_costo')) {
                $table->dropColumn('precio_costo');
            }
            if (Schema::hasColumn('tbl_producto', 'sku')) {
                $table->dropColumn('sku');
            }
        });

        Schema::table('tbl_kardex', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_kardex', 'origen')) {
                $table->string('origen', 50)->nullable();
            }
            if (!Schema::hasColumn('tbl_kardex', 'tipo_movimiento_enum')) {
                $table->enum('tipo_movimiento_enum', ['ENTRADA','SALIDA','AJUSTE'])->after('tipo_movimiento');
            }
        });
    }
};