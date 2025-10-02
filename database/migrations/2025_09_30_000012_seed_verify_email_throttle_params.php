<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $now = now();
        $rows = [
            ['parametro' => 'AUTH.VERIFY_EMAIL.COOLDOWN_MINUTES', 'valor' => '5'],
            ['parametro' => 'AUTH.VERIFY_EMAIL.MAX_PER_DAY', 'valor' => '5'],
            ['parametro' => 'AUTH.VERIFY_EMAIL.DELETE_AFTER_DAYS', 'valor' => '30'],
        ];

        foreach ($rows as $row) {
            $exists = DB::table('tbl_parametros')->where('parametro', $row['parametro'])->exists();
            if (!$exists) {
                DB::table('tbl_parametros')->insert([
                    'parametro' => $row['parametro'],
                    'valor' => $row['valor'],
                    'id_usuario_fk' => 1,
                    'creado_por' => 'system',
                    'fecha_creacion' => $now,
                    'modificado_por' => 'system',
                    'fecha_modificacion' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('tbl_parametros')->whereIn('parametro', [
            'AUTH.VERIFY_EMAIL.COOLDOWN_MINUTES',
            'AUTH.VERIFY_EMAIL.MAX_PER_DAY',
            'AUTH.VERIFY_EMAIL.DELETE_AFTER_DAYS',
        ])->delete();
    }
};
