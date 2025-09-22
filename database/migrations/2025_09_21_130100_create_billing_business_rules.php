<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Trigger para validar rango CAI y fecha límite en facturas
        DB::statement("
            CREATE TRIGGER trg_factura_cai_validation 
            BEFORE INSERT ON tbl_factura
            FOR EACH ROW
            BEGIN
                DECLARE v_rango_inicio INT;
                DECLARE v_rango_fin INT;
                DECLARE v_fecha_limite DATE;
                DECLARE v_consecutivo_actual INT;
                DECLARE v_numero_factura INT;
                
                -- Obtener datos del CAI
                SELECT 
                    CAST(rango_inicio AS UNSIGNED),
                    CAST(rango_fin AS UNSIGNED),
                    fecha_limite,
                    consecutivo_actual
                INTO v_rango_inicio, v_rango_fin, v_fecha_limite, v_consecutivo_actual
                FROM tbl_cai 
                WHERE id_cai_pk = NEW.id_cai_fk;
                
                -- Extraer número de factura (asumir formato numérico o extraer parte numérica)
                SET v_numero_factura = CAST(NEW.numero AS UNSIGNED);
                
                -- Validar que el número esté en el rango
                IF v_numero_factura < v_rango_inicio OR v_numero_factura > v_rango_fin THEN
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = 'El número de factura está fuera del rango autorizado por el CAI';
                END IF;
                
                -- Validar que la fecha no supere la fecha límite
                IF DATE(NEW.fecha) > v_fecha_limite THEN
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = 'La fecha de la factura supera la fecha límite del CAI';
                END IF;
                
                -- Validar que el número sea secuencial (mayor al consecutivo actual)
                IF v_numero_factura <= v_consecutivo_actual THEN
                    SIGNAL SQLSTATE '45000' 
                    SET MESSAGE_TEXT = 'El número de factura debe ser mayor al consecutivo actual';
                END IF;
            END
        ");

        // 2. Trigger para actualizar consecutivo actual después de insertar factura
        DB::statement("
            CREATE TRIGGER trg_factura_update_consecutivo 
            AFTER INSERT ON tbl_factura
            FOR EACH ROW
            BEGIN
                DECLARE v_numero_factura INT;
                
                SET v_numero_factura = CAST(NEW.numero AS UNSIGNED);
                
                -- Actualizar consecutivo actual en CAI
                UPDATE tbl_cai 
                SET consecutivo_actual = v_numero_factura 
                WHERE id_cai_pk = NEW.id_cai_fk 
                AND v_numero_factura > consecutivo_actual;
            END
        ");

        // 3. Trigger para recalcular totales de factura basado en detalle_factura
        DB::statement("
            CREATE TRIGGER trg_detalle_factura_recalc_totales 
            AFTER INSERT ON tbl_detalle_factura
            FOR EACH ROW
            BEGIN
                UPDATE tbl_factura 
                SET 
                    subtotal = (
                        SELECT COALESCE(SUM(total_linea), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = NEW.id_factura_fk
                    ),
                    impuesto = (
                        SELECT COALESCE(SUM(impuesto), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = NEW.id_factura_fk
                    ),
                    total = (
                        SELECT COALESCE(SUM(total_linea) + SUM(impuesto), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = NEW.id_factura_fk
                    )
                WHERE id_factura_pk = NEW.id_factura_fk;
            END
        ");

        // 4. Trigger similar para UPDATE y DELETE en detalle_factura
        DB::statement("
            CREATE TRIGGER trg_detalle_factura_recalc_totales_upd 
            AFTER UPDATE ON tbl_detalle_factura
            FOR EACH ROW
            BEGIN
                UPDATE tbl_factura 
                SET 
                    subtotal = (
                        SELECT COALESCE(SUM(total_linea), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = NEW.id_factura_fk
                    ),
                    impuesto = (
                        SELECT COALESCE(SUM(impuesto), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = NEW.id_factura_fk
                    ),
                    total = (
                        SELECT COALESCE(SUM(total_linea) + SUM(impuesto), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = NEW.id_factura_fk
                    )
                WHERE id_factura_pk = NEW.id_factura_fk;
            END
        ");

        DB::statement("
            CREATE TRIGGER trg_detalle_factura_recalc_totales_del 
            AFTER DELETE ON tbl_detalle_factura
            FOR EACH ROW
            BEGIN
                UPDATE tbl_factura 
                SET 
                    subtotal = (
                        SELECT COALESCE(SUM(total_linea), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = OLD.id_factura_fk
                    ),
                    impuesto = (
                        SELECT COALESCE(SUM(impuesto), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = OLD.id_factura_fk
                    ),
                    total = (
                        SELECT COALESCE(SUM(total_linea) + SUM(impuesto), 0) 
                        FROM tbl_detalle_factura 
                        WHERE id_factura_fk = OLD.id_factura_fk
                    )
                WHERE id_factura_pk = OLD.id_factura_fk;
            END
        ");
    }

    public function down(): void
    {
        DB::statement("DROP TRIGGER IF EXISTS trg_detalle_factura_recalc_totales_del");
        DB::statement("DROP TRIGGER IF EXISTS trg_detalle_factura_recalc_totales_upd");
        DB::statement("DROP TRIGGER IF EXISTS trg_detalle_factura_recalc_totales");
        DB::statement("DROP TRIGGER IF EXISTS trg_factura_update_consecutivo");
        DB::statement("DROP TRIGGER IF EXISTS trg_factura_cai_validation");
    }
};