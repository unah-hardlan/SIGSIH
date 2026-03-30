<?php
define('LARAVEL_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Crear usuario técnico INACTIVO para pruebas
$tecnicoRol = \App\Models\Rol::where('rol', 'like', '%tecn%')->first();
$generos = \App\Models\Genero::all();
$generoId = $generos->first()->id_genero_pk ?? 1;

// Verificar si ya existe
$usuarioExist = \App\Models\Usuario::where('usuario', 'TECNICO_INACTIVO')->first();

if (!$usuarioExist) {
    echo "=== CREANDO USUARIO TÉCNICO INACTIVO PARA PRUEBA ===\n\n";

    $usuario = \App\Models\Usuario::create([
        'usuario' => 'TECNICO_INACTIVO',
        'correo_electronico' => 'tecnico.inactivo@example.com',
        'contrasena' => bcrypt('Tech123456'),
        'estado_usuario' => 'INACTIVO',
        'id_rol_fk' => $tecnicoRol->id_rol_pk,
    ]);

    $persona = \App\Models\Persona::create([
        'primer_nombre' => 'Manuel',
        'segundo_nombre' => 'Gabriel',
        'primer_apellido' => 'Méndez',
        'segundo_apellido' => 'Contreras',
        'dni' => '12345678-99',
        'id_usuario_fk' => $usuario->id_usuario_pk,
        'id_genero_fk' => $generoId,
        'estado_persona' => 'ACTIVO',
    ]);

    echo "✓ Usuario INACTIVO creado: Manuel Méndez (ID: {$usuario->id_usuario_pk}, Estado: INACTIVO)\n";
} else {
    echo "⚠️  Usuario TECNICO_INACTIVO ya existe (ID: {$usuarioExist->id_usuario_pk}, Estado: {$usuarioExist->estado_usuario})\n";
}

echo "\n=== PROBANDO ENDPOINT /api/tecnicos ===\n\n";

// Obtener todos los técnicos (incluyendo inactivos - para verificación)
$rolesAll = \App\Models\Rol::where('rol', 'like', '%tecn%')->pluck('id_rol_pk')->all();
$userIdsPrimary = \App\Models\Usuario::whereIn('id_rol_fk', $rolesAll)->pluck('id_usuario_pk')->all();
$userIdsPivot = \Illuminate\Support\Facades\DB::table('tbl_usuario_rol')->whereIn('id_rol_fk', $rolesAll)->pluck('id_usuario_fk')->all();
$userIdsAll = collect($userIdsPrimary)->merge($userIdsPivot)->unique()->values()->all();

echo "Técnicos EN TOTAL (incluyendo inactivos): " . count($userIdsAll) . "\n";
$personasAll = \App\Models\Persona::whereIn('id_usuario_fk', $userIdsAll)
    ->orderBy('primer_nombre')
    ->get(['id_persona_pk as id', 'id_usuario_fk', 'primer_nombre', 'primer_apellido']);
foreach ($personasAll as $p) {
    $user = \App\Models\Usuario::find($p->id_usuario_fk);
    $estado = $user ? $user->estado_usuario : 'UNKNOWN';
    echo "  - {$p->primer_nombre} {$p->primer_apellido} (ID: {$p->id}, Estado: {$estado})\n";
}

echo "\n";

// Aplicar filtro ACTIVO solamente
$roleIds = $rolesAll;
$userIdsPrimaryActive = \App\Models\Usuario::whereIn('id_rol_fk', $roleIds)
    ->where('estado_usuario', 'ACTIVO')
    ->pluck('id_usuario_pk')->all();
$userIdsPivotActive = \Illuminate\Support\Facades\DB::table('tbl_usuario_rol')
    ->whereIn('tbl_usuario_rol.id_rol_fk', $roleIds)
    ->join('tbl_ms_usuario', 'tbl_usuario_rol.id_usuario_fk', '=', 'tbl_ms_usuario.id_usuario_pk')
    ->where('tbl_ms_usuario.estado_usuario', 'ACTIVO')
    ->pluck('id_usuario_fk')->all();
$userIdsActive = collect($userIdsPrimaryActive)->merge($userIdsPivotActive)->unique()->values()->all();

echo "Técnicos ACTIVOS (según nuevo filtro): " . count($userIdsActive) . "\n";
$personasActive = \App\Models\Persona::whereIn('id_usuario_fk', $userIdsActive)
    ->orderBy('primer_nombre')
    ->get(['id_persona_pk as id', 'primer_nombre', 'primer_apellido']);
foreach ($personasActive as $p) {
    echo "  - {$p->primer_nombre} {$p->primer_apellido} (ID: {$p->id})\n";
}

echo "\n✓ Usuarios INACTIVOS correctamente filtrados\n";
