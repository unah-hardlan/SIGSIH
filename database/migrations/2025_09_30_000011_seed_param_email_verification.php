<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Habilitar verificación de correo por defecto = 1 (true)
        $now = now();
        $exists = DB::table('tbl_parametros')->where('parametro', 'AUTH.REQUIERE_VERIFICACION_CORREO')->exists();
        if (!$exists) {
            DB::table('tbl_parametros')->insert([
                'parametro' => 'AUTH.REQUIERE_VERIFICACION_CORREO',
                'valor' => '1',
                'id_usuario_fk' => 1,
                'fecha_creacion' => $now,
                'creado_por' => 'system',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_parametros')->where('parametro', 'AUTH.REQUIERE_VERIFICACION_CORREO')->delete();
    }
};
