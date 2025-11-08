<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema as DBSchema;

class PruneBitacora extends Command
{
    
    protected $signature = 'bitacora:prune {--keep=100 : Cantidad de registros a conservar (más recientes)}';

    
    protected $description = 'Podar la bitácora manteniendo sólo los N registros más recientes';

    
    public function handle(): int
    {
        $keep = (int) $this->option('keep');
        if ($keep < 1) {
            $keep = 100;
        }

        
        $table = 'tbl_ms_bitacora';
        $idCol = 'id_bitacora_pk';
        $dateCol = 'fecha_creacion'; 

        
        if (!DBSchema::hasTable($table)) {
            $this->warn("Tabla {$table} no encontrada; se omite poda.");
            return self::SUCCESS;
        }
        if (!DBSchema::hasColumn($table, $idCol) || !DBSchema::hasColumn($table, $dateCol)) {
            $this->warn("Columnas requeridas no encontradas en {$table}; se omite poda.");
            return self::SUCCESS;
        }

        try {
            $driver = DB::getDriverName();
            switch ($driver) {
                case 'sqlsrv':
                    
                    $sql = "WITH cte AS (\n  SELECT {$idCol} AS id, ROW_NUMBER() OVER (ORDER BY {$dateCol} DESC, {$idCol} DESC) AS rn\n  FROM {$table}\n)\nDELETE b\nFROM {$table} b\nINNER JOIN cte ON cte.id = b.{$idCol}\nWHERE cte.rn > ?;";
                    DB::statement($sql, [$keep]);
                    break;
                case 'pgsql':
                    
                    $sql = "WITH cte AS (\n  SELECT {$idCol} AS id, ROW_NUMBER() OVER (ORDER BY {$dateCol} DESC, {$idCol} DESC) AS rn\n  FROM {$table}\n)\nDELETE FROM {$table} b USING cte\nWHERE cte.id = b.{$idCol} AND cte.rn > ?;";
                    DB::statement($sql, [$keep]);
                    break;
                case 'mysql':
                    
                    $verRow = DB::select('SELECT VERSION() as v');
                    $ver = is_array($verRow) && isset($verRow[0]) ? (string)($verRow[0]->v ?? '') : '';
                    $major = (int)preg_replace('/^(\d+).*/', '$1', $ver);
                    if ($major >= 8) {
                        $sql = "WITH cte AS (\n  SELECT {$idCol} AS id, ROW_NUMBER() OVER (ORDER BY {$dateCol} DESC, {$idCol} DESC) AS rn\n  FROM {$table}\n)\nDELETE b FROM {$table} b\nJOIN cte ON cte.id = b.{$idCol}\nWHERE cte.rn > ?;";
                        DB::statement($sql, [$keep]);
                    } else {
                        
                        $sql = "DELETE FROM {$table}\nWHERE {$idCol} NOT IN (\n  SELECT id FROM (\n    SELECT {$idCol} AS id\n    FROM {$table}\n    ORDER BY {$dateCol} DESC, {$idCol} DESC\n    LIMIT ?\n  ) t\n);";
                        DB::statement($sql, [$keep]);
                    }
                    break;
                case 'sqlite':
                default:
                    
                    $sql = "DELETE FROM {$table}\nWHERE {$idCol} NOT IN (\n  SELECT {$idCol} FROM {$table}\n  ORDER BY {$dateCol} DESC, {$idCol} DESC\n  LIMIT ?\n);";
                    DB::statement($sql, [$keep]);
                    break;
            }

            $left = DB::table($table)->count();
            $this->info("Bitácora podada. Registros actuales: {$left} (conservados {$keep}).");
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error al podar bitácora: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
