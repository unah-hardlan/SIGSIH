<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Crear vista para calcular existencias por producto
        DB::statement("
            CREATE OR REPLACE VIEW v_existencias_producto AS
            SELECT 
                p.id_producto_pk,
                p.sku,
                p.nombre_producto,
                p.stock_minimo,
                p.precio_costo,
                p.precio_venta,
                COALESCE(
                    SUM(CASE WHEN k.tipo_movimiento = 'ENTRADA' THEN k.cantidad ELSE 0 END) -
                    SUM(CASE WHEN k.tipo_movimiento = 'SALIDA' THEN k.cantidad ELSE 0 END) +
                    SUM(CASE WHEN k.tipo_movimiento = 'AJUSTE' THEN k.cantidad ELSE 0 END), 
                    0
                ) as stock_actual,
                CASE 
                    WHEN COALESCE(
                        SUM(CASE WHEN k.tipo_movimiento = 'ENTRADA' THEN k.cantidad ELSE 0 END) -
                        SUM(CASE WHEN k.tipo_movimiento = 'SALIDA' THEN k.cantidad ELSE 0 END) +
                        SUM(CASE WHEN k.tipo_movimiento = 'AJUSTE' THEN k.cantidad ELSE 0 END), 
                        0
                    ) <= p.stock_minimo THEN 'CRITICO'
                    ELSE 'NORMAL'
                END as estado_stock
            FROM tbl_producto p
            LEFT JOIN tbl_kardex k ON p.id_producto_pk = k.id_producto_fk
            GROUP BY 
                p.id_producto_pk, p.sku, p.nombre_producto, 
                p.stock_minimo, p.precio_costo, p.precio_venta
        ");

        // 2. Agregar constraint CHECK para no permitir stock negativo en futuras operaciones
        // Nota: MySQL no soporta CHECK constraints complejos hasta la versión 8.0.16+
        // Como alternativa, crearemos un trigger
        DB::statement("
            CREATE TRIGGER trg_kardex_stock_check 
            BEFORE INSERT ON tbl_kardex
            FOR EACH ROW
            BEGIN
                DECLARE stock_actual INT DEFAULT 0;
                
                SELECT COALESCE(
                    SUM(CASE WHEN tipo_movimiento = 'ENTRADA' THEN cantidad ELSE 0 END) -
                    SUM(CASE WHEN tipo_movimiento = 'SALIDA' THEN cantidad ELSE 0 END) +
                    SUM(CASE WHEN tipo_movimiento = 'AJUSTE' THEN cantidad ELSE 0 END), 
                    0
                ) INTO stock_actual
                FROM tbl_kardex 
                WHERE id_producto_fk = NEW.id_producto_fk;
                
                -- Si es salida, verificar que no genere stock negativo
                IF NEW.tipo_movimiento = 'SALIDA' AND (stock_actual - NEW.cantidad) < 0 THEN
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = 'No se puede generar stock negativo. Stock actual insuficiente.';
                END IF;
                
                -- Si es ajuste negativo, verificar que no genere stock negativo
                IF NEW.tipo_movimiento = 'AJUSTE' AND NEW.cantidad < 0 AND (stock_actual + NEW.cantidad) < 0 THEN
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = 'El ajuste negativo generaría stock negativo.';
                END IF;
            END
        ");
    }

    public function down(): void
    {
        // Eliminar trigger
        DB::statement("DROP TRIGGER IF EXISTS trg_kardex_stock_check");
        
        // Eliminar vista
        DB::statement("DROP VIEW IF EXISTS v_existencias_producto");
    }
};