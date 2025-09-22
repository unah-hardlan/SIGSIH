<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Agregar consecutivo_actual a tabla CAI
        Schema::table('tbl_cai', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_cai', 'consecutivo_actual')) {
                $table->integer('consecutivo_actual')->default(0)->after('rango_fin');
            }
        });

        // 2. Agregar UNIQUE constraint a numero de factura
        Schema::table('tbl_factura', function (Blueprint $table) {
            // Verificar si ya existe el índice único
            $indexExists = collect(DB::select("SHOW INDEX FROM tbl_factura WHERE Key_name = 'tbl_factura_numero_unique'"))->isNotEmpty();
            
            if (!$indexExists) {
                $table->unique('numero', 'tbl_factura_numero_unique');
            }
        });

        // 3. Mejorar estructura de detalle_factura para que tenga campos de cotización
        Schema::table('tbl_detalle_factura', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_detalle_factura', 'descripcion')) {
                $table->string('descripcion', 255)->after('id_servicio_fk');
            }
            if (!Schema::hasColumn('tbl_detalle_factura', 'precio_unitario')) {
                $table->decimal('precio_unitario', 12, 2)->after('descripcion');
            }
            if (!Schema::hasColumn('tbl_detalle_factura', 'cantidad')) {
                $table->decimal('cantidad', 8, 2)->default(1)->after('precio_unitario');
            }
            if (!Schema::hasColumn('tbl_detalle_factura', 'impuesto')) {
                $table->decimal('impuesto', 12, 2)->default(0)->after('cantidad');
            }
            if (!Schema::hasColumn('tbl_detalle_factura', 'total_linea')) {
                $table->decimal('total_linea', 12, 2)->after('impuesto');
            }
        });

        // 4. Agregar campos de cálculo a factura si faltan
        Schema::table('tbl_factura', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_factura', 'impuesto')) {
                $table->decimal('impuesto', 12, 2)->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('tbl_factura', 'descuento')) {
                $table->decimal('descuento', 12, 2)->default(0)->after('impuesto');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_factura', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_factura', 'descuento')) {
                $table->dropColumn('descuento');
            }
            if (Schema::hasColumn('tbl_factura', 'impuesto')) {
                $table->dropColumn('impuesto');
            }
            $table->dropUnique('tbl_factura_numero_unique');
        });

        Schema::table('tbl_detalle_factura', function (Blueprint $table) {
            $columns = ['total_linea', 'impuesto', 'cantidad', 'precio_unitario', 'descripcion'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('tbl_detalle_factura', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('tbl_cai', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_cai', 'consecutivo_actual')) {
                $table->dropColumn('consecutivo_actual');
            }
        });
    }
};