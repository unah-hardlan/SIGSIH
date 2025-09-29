<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_parametros')) {
            return;
        }

        $now = Carbon::now();

        $creatorId = null;
        if (Schema::hasTable('tbl_ms_usuario')) {
            $creatorId = DB::table('tbl_ms_usuario')
                ->whereRaw('UPPER(usuario) = ?', ['ADMIN'])
                ->value('id_usuario_pk');

            if (!$creatorId) {
                $creatorId = DB::table('tbl_ms_usuario')
                    ->orderBy('id_usuario_pk')
                    ->value('id_usuario_pk');
            }
        }

        if (!$creatorId) {
            $creatorId = 1;
        }

        $parameters = [
            [
                'names' => ['auth.password_reset.max_per_day', 'AUTH.PASSWORD_RESET.MAX_PER_DAY'],
                'value' => '5',
            ],
            [
                'names' => ['auth.password_reset.cooldown_minutes', 'AUTH.PASSWORD_RESET.COOLDOWN_MINUTES'],
                'value' => '5',
            ],
            [
                'names' => ['auth.password_reset.expire_minutes', 'AUTH.PASSWORD_RESET.EXPIRE_MINUTES'],
                'value' => '60',
            ],
        ];

        foreach ($parameters as $parameter) {
            foreach ($parameter['names'] as $name) {
                $exists = DB::table('tbl_parametros')->where('parametro', $name)->exists();
                if ($exists) {
                    continue;
                }

                DB::table('tbl_parametros')->insert([
                    'parametro' => $name,
                    'valor' => $parameter['value'],
                    'id_usuario_fk' => $creatorId,
                    'creado_por' => 'system',
                    'fecha_creacion' => $now,
                    'modificado_por' => null,
                    'fecha_modificacion' => null,
                ]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('tbl_parametros')) {
            return;
        }

        DB::table('tbl_parametros')->whereIn('parametro', [
            'auth.password_reset.max_per_day',
            'AUTH.PASSWORD_RESET.MAX_PER_DAY',
            'auth.password_reset.cooldown_minutes',
            'AUTH.PASSWORD_RESET.COOLDOWN_MINUTES',
            'auth.password_reset.expire_minutes',
            'AUTH.PASSWORD_RESET.EXPIRE_MINUTES',
        ])->delete();
    }
};
