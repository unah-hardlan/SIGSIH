<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Map old => new
        $map = [
            'app.name' => 'APP.NOMBRE',
            'app.logo_path' => 'APP.LOGO_RUTA',
            'app.logo_height' => 'APP.LOGO_ALTO',
            'app.timezone' => 'APP.ZONA_HORARIA',
            'app.date_format' => 'APP.FORMATO_FECHA',
            'auth.sessions_limit' => 'AUTH.LIMITE_SESIONES',
            'ADMIN_INTENTOS_INICIO SESION' => 'ADMIN.INTENTOS_INICIO_SESION',
            'ADMIN_CORREO' => 'ADMIN.CORREO',
            'ADMIN_CUSER' => 'ADMIN.USUARIO',
            'ADMIN_CPASS' => 'ADMIN.PASSWORD',
        ];
        foreach ($map as $old => $new) {
            DB::table('tbl_parametros')->where('parametro', $old)->update(['parametro' => $new]);
        }
    }

    public function down(): void
    {
        // Reverse map
        $map = [
            'APP.NOMBRE' => 'app.name',
            'APP.LOGO_RUTA' => 'app.logo_path',
            'APP.LOGO_ALTO' => 'app.logo_height',
            'APP.ZONA_HORARIA' => 'app.timezone',
            'APP.FORMATO_FECHA' => 'app.date_format',
            'AUTH.LIMITE_SESIONES' => 'auth.sessions_limit',
            'ADMIN.INTENTOS_INICIO_SESION' => 'ADMIN_INTENTOS_INICIO SESION',
            'ADMIN.CORREO' => 'ADMIN_CORREO',
            'ADMIN.USUARIO' => 'ADMIN_CUSER',
            'ADMIN.PASSWORD' => 'ADMIN_CPASS',
        ];
        foreach ($map as $old => $new) {
            DB::table('tbl_parametros')->where('parametro', $old)->update(['parametro' => $new]);
        }
    }
};
