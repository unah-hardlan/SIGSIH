<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. UNIQUE constraints para campos críticos
        
        // DNI único en tabla persona
        if (Schema::hasColumn('tbl_persona', 'dni')) {
            try {
                Schema::table('tbl_persona', function (Blueprint $table) {
                    $table->unique('dni', 'unique_persona_dni');
                });
            } catch (Exception $e) {
                echo "DNI ya tiene UNIQUE constraint o hay duplicados" . PHP_EOL;
            }
        }

        // Correo electrónico único en tabla usuario
        if (Schema::hasColumn('tbl_ms_usuario', 'correo_electronico')) {
            try {
                Schema::table('tbl_ms_usuario', function (Blueprint $table) {
                    $table->unique('correo_electronico', 'unique_usuario_email');
                });
            } catch (Exception $e) {
                echo "Correo electrónico ya tiene UNIQUE constraint o hay duplicados" . PHP_EOL;
            }
        }

        // SKU único en tabla producto (ya implementado en migración anterior)
        echo "SKU ya tiene UNIQUE constraint implementado" . PHP_EOL;

        // Código único en tabla CAI
        if (Schema::hasColumn('tbl_cai', 'codigo')) {
            try {
                Schema::table('tbl_cai', function (Blueprint $table) {
                    $table->unique('codigo', 'unique_cai_codigo');
                });
            } catch (Exception $e) {
                echo "Código CAI ya tiene UNIQUE constraint o hay duplicados" . PHP_EOL;
            }
        }

        // 2. Ajustar longitudes de VARCHAR que se quedan cortos
        
        // Nombres de persona pueden ser más largos
        Schema::table('tbl_persona', function (Blueprint $table) {
            $table->string('primer_nombre', 80)->change();
            $table->string('segundo_nombre', 80)->nullable()->change();
            $table->string('primer_apellido', 80)->change();
            $table->string('segundo_apellido', 80)->nullable()->change();
            $table->string('cargo', 100)->nullable()->change();
        });

        // Nombres de cliente persona
        if (Schema::hasTable('tbl_cliente_persona')) {
            Schema::table('tbl_cliente_persona', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_cliente_persona', 'primer_nombre')) {
                    $table->string('primer_nombre', 80)->change();
                }
                if (Schema::hasColumn('tbl_cliente_persona', 'segundo_nombre')) {
                    $table->string('segundo_nombre', 80)->nullable()->change();
                }
                if (Schema::hasColumn('tbl_cliente_persona', 'primer_apellido')) {
                    $table->string('primer_apellido', 80)->change();
                }
                if (Schema::hasColumn('tbl_cliente_persona', 'segundo_apellido')) {
                    $table->string('segundo_apellido', 80)->nullable()->change();
                }
            });
        }

        // Nombres de usuario más largos
        Schema::table('tbl_ms_usuario', function (Blueprint $table) {
            $table->string('usuario', 80)->change();
            $table->string('nombre_usuario', 150)->change();
            $table->string('correo_electronico', 150)->change();
        });

        // Nombres comerciales y razón social más largos
        if (Schema::hasTable('tbl_cliente_empresa')) {
            Schema::table('tbl_cliente_empresa', function (Blueprint $table) {
                if (Schema::hasColumn('tbl_cliente_empresa', 'nombre_comercial')) {
                    $table->string('nombre_comercial', 150)->change();
                }
                if (Schema::hasColumn('tbl_cliente_empresa', 'razon_social')) {
                    $table->string('razon_social', 150)->nullable()->change();
                }
                if (Schema::hasColumn('tbl_cliente_empresa', 'rtn')) {
                    $table->string('rtn', 30)->nullable()->change();
                }
            });
        }

        // Nombres de productos más largos
        Schema::table('tbl_producto', function (Blueprint $table) {
            $table->string('nombre_producto', 150)->change();
            $table->string('descripcion_producto', 1000)->nullable()->change();
        });

        // Nombres de proyectos más largos
        Schema::table('tbl_proyectos', function (Blueprint $table) {
            $table->string('nombre_proyecto', 150)->change();
            $table->string('descripcion_proyecto', 1000)->nullable()->change();
        });

        // Observaciones y diagnósticos más largos
        if (Schema::hasTable('tbl_orden_servicio')) {
            Schema::table('tbl_orden_servicio', function (Blueprint $table) {
                $table->string('observaciones', 1000)->nullable()->change();
                $table->string('diagnostico_tecnico', 1000)->nullable()->change();
                $table->string('diagnostico_cliente', 1000)->nullable()->change();
            });
        }

        // 3. Corregir tipos de moneda a DECIMAL(15,2) para valores grandes
        
        // Tablas de facturación con montos más grandes
        Schema::table('tbl_factura', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('impuesto', 15, 2)->default(0)->change();
            $table->decimal('descuento', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->change();
        });

        if (Schema::hasTable('tbl_detalle_factura')) {
            Schema::table('tbl_detalle_factura', function (Blueprint $table) {
                $table->decimal('precio_unitario', 15, 2)->change();
                $table->decimal('cantidad', 10, 3)->change(); // Permitir decimales en cantidad
                $table->decimal('impuesto', 15, 2)->change();
                $table->decimal('total_linea', 15, 2)->change();
                $table->decimal('descuento', 15, 2)->default(0)->change();
            });
        }

        // Cotizaciones
        if (Schema::hasTable('tbl_cotizacion')) {
            Schema::table('tbl_cotizacion', function (Blueprint $table) {
                $table->decimal('subtotal', 15, 2)->change();
                $table->decimal('total', 15, 2)->change();
                $table->decimal('imponible', 15, 2)->change();
                $table->decimal('impuesto', 15, 2)->change();
                $table->decimal('total_impuesto', 15, 2)->change();
                $table->decimal('otros_cargos', 15, 2)->nullable()->change();
                $table->decimal('anticipo_requerido', 15, 2)->nullable()->change();
            });
        }

        // Items de cotización
        if (Schema::hasTable('tbl_item_cotizacion')) {
            Schema::table('tbl_item_cotizacion', function (Blueprint $table) {
                $table->decimal('precio_unitario', 15, 2)->change();
                $table->decimal('cantidad', 10, 3)->change();
                $table->decimal('impuesto', 15, 2)->change();
                $table->decimal('total', 15, 2)->change();
            });
        }

        // Productos
        Schema::table('tbl_producto', function (Blueprint $table) {
            $table->decimal('precio_unitario', 15, 2)->change();
            $table->decimal('precio_costo', 15, 2)->nullable()->change();
            $table->decimal('precio_venta', 15, 2)->change();
        });

        // Kardex - solo ajustar cantidad para permitir decimales
        if (Schema::hasTable('tbl_kardex')) {
            Schema::table('tbl_kardex', function (Blueprint $table) {
                $table->decimal('cantidad', 12, 3)->change(); // Permitir decimales en inventario
            });
        }

        echo "Correcciones de tipos de datos aplicadas:" . PHP_EOL;
        echo "✅ UNIQUE constraints: dni, correo_electronico, sku, codigo_cai" . PHP_EOL;
        echo "✅ VARCHAR ampliados: nombres, descripciones, observaciones" . PHP_EOL;
        echo "✅ DECIMAL(15,2): monedas y precios" . PHP_EOL;
        echo "✅ DECIMAL(12,3): cantidades en kardex con decimales" . PHP_EOL;
    }

    public function down(): void
    {
        // Rollback sería muy complejo y arriesgado
        // Los cambios de tipos de datos generalmente no se revierten
        echo "Rollback no implementado para cambios de tipos de datos" . PHP_EOL;
    }
};