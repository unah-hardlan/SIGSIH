<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Remove previous variants and consolidate into one
        DB::table('tbl_parametros')
            ->whereIn('parametro', ['ESTRUCTURA DNI', 'Formato de DNI', 'FORMATO DE DNI'])
            ->delete();

        $now = now();
        $param = 'FORMATO DNI';
        $default = '0000-0000-00000'; // mask style, one single parameter as requested
        $exists = DB::table('tbl_parametros')->where('parametro', $param)->exists();
        if (!$exists) {
            DB::table('tbl_parametros')->insert([
                'parametro' => $param,
                'valor' => $default,
                'id_usuario_fk' => 1,
                'creado_por' => 'system',
                'fecha_creacion' => $now,
                'modificado_por' => 'system',
                'fecha_modificacion' => $now,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_parametros')->where('parametro', 'FORMATO DNI')->delete();
        // Optionally restore one of the old variants
        DB::table('tbl_parametros')->insert([
            'parametro' => 'ESTRUCTURA DNI',
            'valor' => '^(\\d{13}|\\d{4}-\\d{4}-\\d{5})$',
            'id_usuario_fk' => 1,
            'creado_por' => 'system',
            'fecha_creacion' => now(),
            'modificado_por' => 'system',
            'fecha_modificacion' => now(),
        ]);
    }
};
