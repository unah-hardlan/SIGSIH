<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Create estado catalog for Orden de Servicio with standardized columns
        if (!Schema::hasTable('tbl_estado_orden_servicio')) {
            Schema::create('tbl_estado_orden_servicio', function (Blueprint $t) {
                $t->increments('id_estado_orden_servicio_pk');
                $t->string('codigo', 50)->nullable();
                $t->string('nombre', 100)->nullable();
                $t->string('descripcion', 255)->nullable();
                $t->boolean('es_final')->default(false);
                $t->unsignedSmallInteger('orden')->default(0);
                $t->unique('codigo', 'uniq_tbl_estado_os_codigo');
                $t->index('orden', 'idx_tbl_estado_os_orden');
            });
        }

        // 2) Alter tbl_orden_servicio: add numero_orden_servicio, timestamps, estado fk
        if (Schema::hasTable('tbl_orden_servicio')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $t) {
                if (!Schema::hasColumn('tbl_orden_servicio', 'numero_orden_servicio')) {
                    $t->string('numero_orden_servicio', 20)->nullable();
                    $t->unique('numero_orden_servicio', 'uniq_tbl_os_numero');
                }
                if (!Schema::hasColumn('tbl_orden_servicio', 'fecha_creada')) {
                    $t->dateTime('fecha_creada')->nullable()->after('id_tecnico_fk');
                }
                if (!Schema::hasColumn('tbl_orden_servicio', 'fecha_asignada')) {
                    $t->dateTime('fecha_asignada')->nullable()->after('fecha_creada');
                }
                if (!Schema::hasColumn('tbl_orden_servicio', 'id_estado_orden_servicio_fk')) {
                    $t->unsignedInteger('id_estado_orden_servicio_fk')->nullable()->after('fecha_asignada');
                    $t->foreign('id_estado_orden_servicio_fk','fk_os_estado')
                        ->references('id_estado_orden_servicio_pk')->on('tbl_estado_orden_servicio')
                        ->onDelete('restrict')->onUpdate('cascade');
                }
            });

            // Backfill fecha_creada from fecha_recepcion if null
            DB::statement("UPDATE tbl_orden_servicio SET fecha_creada = COALESCE(fecha_creada, fecha_recepcion, NOW())");
        }

        // 3) Link Cotizacion -> OrdenServicio (add FK) and backfill using OS.id_cotizacion_fk
        if (Schema::hasTable('tbl_cotizacion')) {
            // Detect PK type and signedness of tbl_orden_servicio.id_orden_servicio_pk
            $osPk = DB::table('information_schema.COLUMNS')
                ->select('DATA_TYPE','COLUMN_TYPE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'tbl_orden_servicio')
                ->where('COLUMN_NAME', 'id_orden_servicio_pk')
                ->first();
            $isOsPkBigInt = $osPk && (str_contains($osPk->DATA_TYPE, 'bigint') || str_contains($osPk->COLUMN_TYPE, 'bigint'));
            $isOsPkUnsigned = $osPk && str_contains(strtolower($osPk->COLUMN_TYPE), 'unsigned');

            Schema::table('tbl_cotizacion', function (Blueprint $t) use ($isOsPkBigInt, $isOsPkUnsigned) {
                if (!Schema::hasColumn('tbl_cotizacion', 'id_orden_servicio_fk')) {
                    if ($isOsPkBigInt) {
                        $col = $t->bigInteger('id_orden_servicio_fk')->nullable()->after('id_cliente_fk');
                    } else {
                        $col = $t->integer('id_orden_servicio_fk')->nullable()->after('id_cliente_fk');
                    }
                    if ($isOsPkUnsigned) {
                        $col->unsigned();
                    }
                    $t->foreign('id_orden_servicio_fk','fk_cot_os')
                        ->references('id_orden_servicio_pk')->on('tbl_orden_servicio')
                        ->onDelete('cascade')->onUpdate('cascade');
                }
            });

            // Backfill c.id_orden_servicio_fk from os.id_cotizacion_fk
            if (Schema::hasTable('tbl_orden_servicio') && Schema::hasColumn('tbl_orden_servicio', 'id_cotizacion_fk')) {
                DB::statement("UPDATE tbl_cotizacion c JOIN tbl_orden_servicio os ON os.id_cotizacion_fk = c.id_cotizacion_pk SET c.id_orden_servicio_fk = os.id_orden_servicio_pk WHERE c.id_orden_servicio_fk IS NULL");
            }
        }

        // 4) Link Factura -> Cotizacion (add FK), keep existing client for now
        if (Schema::hasTable('tbl_factura')) {
            // Detect PK type and signedness of tbl_cotizacion.id_cotizacion_pk
            $cotPk = DB::table('information_schema.COLUMNS')
                ->select('DATA_TYPE','COLUMN_TYPE')
                ->where('TABLE_SCHEMA', DB::getDatabaseName())
                ->where('TABLE_NAME', 'tbl_cotizacion')
                ->where('COLUMN_NAME', 'id_cotizacion_pk')
                ->first();
            $isCotPkBigInt = $cotPk && (str_contains($cotPk->DATA_TYPE, 'bigint') || str_contains($cotPk->COLUMN_TYPE, 'bigint'));
            $isCotPkUnsigned = $cotPk && str_contains(strtolower($cotPk->COLUMN_TYPE), 'unsigned');

            Schema::table('tbl_factura', function (Blueprint $t) use ($isCotPkBigInt, $isCotPkUnsigned) {
                if (!Schema::hasColumn('tbl_factura', 'id_cotizacion_fk')) {
                    if ($isCotPkBigInt) {
                        $col = $t->bigInteger('id_cotizacion_fk')->nullable()->after('id_cai_fk');
                    } else {
                        $col = $t->integer('id_cotizacion_fk')->nullable()->after('id_cai_fk');
                    }
                    if ($isCotPkUnsigned) {
                        $col->unsigned();
                    }
                    $t->foreign('id_cotizacion_fk','fk_fact_cot')
                        ->references('id_cotizacion_pk')->on('tbl_cotizacion')
                        ->onDelete('cascade')->onUpdate('cascade');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tbl_factura')) {
            Schema::table('tbl_factura', function (Blueprint $t) {
                try { $t->dropForeign('fk_fact_cot'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('tbl_factura', 'id_cotizacion_fk')) {
                    $t->dropColumn('id_cotizacion_fk');
                }
            });
        }

        if (Schema::hasTable('tbl_cotizacion')) {
            Schema::table('tbl_cotizacion', function (Blueprint $t) {
                try { $t->dropForeign('fk_cot_os'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('tbl_cotizacion', 'id_orden_servicio_fk')) {
                    $t->dropColumn('id_orden_servicio_fk');
                }
            });
        }

        if (Schema::hasTable('tbl_orden_servicio')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $t) {
                try { $t->dropForeign('fk_os_estado'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('tbl_orden_servicio', 'id_estado_orden_servicio_fk')) {
                    $t->dropColumn('id_estado_orden_servicio_fk');
                }
                try { $t->dropUnique('uniq_tbl_os_numero'); } catch (\Throwable $e) {}
                if (Schema::hasColumn('tbl_orden_servicio', 'numero_orden_servicio')) {
                    $t->dropColumn('numero_orden_servicio');
                }
                if (Schema::hasColumn('tbl_orden_servicio', 'fecha_asignada')) {
                    $t->dropColumn('fecha_asignada');
                }
                if (Schema::hasColumn('tbl_orden_servicio', 'fecha_creada')) {
                    $t->dropColumn('fecha_creada');
                }
            });
        }

        if (Schema::hasTable('tbl_estado_orden_servicio')) {
            Schema::dropIfExists('tbl_estado_orden_servicio');
        }
    }
};
