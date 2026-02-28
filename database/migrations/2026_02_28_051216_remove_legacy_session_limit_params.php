<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $legacyKeys = [
        'AUTH.LIMITE_SESIONES',
        'AUTH.LIMITE_SESIONES.ADMIN',
        'AUTH.LIMITE_SESIONES.CLIENTE',
    ];

    /**
     * Migra el valor legacy a auth.sessions_limit (si aún no existe)
     * y elimina todos los parámetros obsoletos.
     */
    public function up(): void
    {
        if (!Schema::hasTable('tbl_parametros')) {
            return;
        }

        $canonical = DB::table('tbl_parametros')
            ->where('parametro', 'auth.sessions_limit')
            ->value('valor');

        if (!is_numeric($canonical)) {
            foreach ($this->legacyKeys as $key) {
                $legacy = DB::table('tbl_parametros')->where('parametro', $key)->value('valor');
                if (is_numeric($legacy) && (int) $legacy > 0) {
                    DB::table('tbl_parametros')->updateOrInsert(
                        ['parametro' => 'auth.sessions_limit'],
                        [
                            'valor'              => (int) $legacy,
                            'modificado_por'     => 'migration',
                            'fecha_modificacion' => now(),
                        ]
                    );
                    break;
                }
            }
        }

        DB::table('tbl_parametros')->whereIn('parametro', $this->legacyKeys)->delete();
    }

    public function down(): void
    {
        // Irreversible: los registros eliminados no se pueden restaurar genéricamente.
    }
};
