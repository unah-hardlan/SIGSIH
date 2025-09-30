<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Valor por defecto: 13 dígitos o con guiones 0000-0000-00000
        $param = 'ESTRUCTURA DNI';
        $valor = '^(\\d{13}|\\d{4}-\\d{4}-\\d{5})$';

        $exists = DB::table('tbl_parametros')->where('parametro', $param)->exists();
        if (!$exists) {
            DB::table('tbl_parametros')->insert([
                'parametro' => $param,
                'valor' => $valor,
                'id_usuario_fk' => 1,
                'creado_por' => 'system',
                'fecha_creacion' => now(),
                'modificado_por' => 'system',
                'fecha_modificacion' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('tbl_parametros')->where('parametro', 'ESTRUCTURA DNI')->delete();
    }
};
