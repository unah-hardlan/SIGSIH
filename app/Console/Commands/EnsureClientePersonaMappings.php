<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;
use App\Models\Persona;

class EnsureClientePersonaMappings extends Command
{
    
    protected $signature = 'clientes:ensure-mappings {--dry-run : Only show what would change}';

    
    protected $description = 'Ensure every Cliente has at least one Persona mapped in tbl_cliente_persona (creating a minimal Persona when missing)';

    
    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');
        $generoId = DB::table('tbl_genero')->min('id_genero_pk');
        
        $usuarioSeq = (int) DB::table('tbl_ms_usuario')->max('id_usuario_pk');
        $createdPersonas = 0;
        $createdPivots = 0;

        $clientes = Cliente::query()->with('empresa')->get();
        foreach ($clientes as $c) {
            $hasPivot = DB::table('tbl_cliente_persona')->where('id_cliente_fk', $c->id_cliente_pk)->exists();
            if ($hasPivot) continue;

            
            $tipo = is_string($c->tipo_cliente) ? strtolower($c->tipo_cliente) : $c->tipo_cliente;
            if ($tipo === 'empresa') {
                $nombre = optional($c->empresa)->nombre_comercial ?: ('Cliente ' . $c->id_cliente_pk);
                $pData = [
                    'primer_nombre' => $nombre,
                    'primer_apellido' => 'Empresa',
                    'dni' => 'EMPRESA-' . $c->id_cliente_pk,
                ];
            } else {
                $pData = [
                    'primer_nombre' => 'Cliente',
                    'primer_apellido' => (string) $c->id_cliente_pk,
                    'dni' => 'CLIENTE-' . $c->id_cliente_pk,
                ];
            }
            $pData['id_genero_fk'] = $generoId ?: null;
            
            $usuarioSeq++;
            $username = 'CLIENTE_' . $c->id_cliente_pk;
            $email = 'cliente_' . $c->id_cliente_pk . '@example.local';
            
            $roleId = DB::table('tbl_ms_rol')->min('id_rol_pk') ?: 1;
            $userId = DB::table('tbl_ms_usuario')->insertGetId([
                'usuario' => strtoupper($username),
                'nombre_usuario' => $username,
                'estado_usuario' => 'ACTIVO',
                'id_rol_fk' => $roleId,
                'contrasena' => bcrypt('Temp#' . rand(1000, 9999)),
                'correo_electronico' => $email,
                'primer_ingreso' => 1,
                'creado_por' => 'system',
                'fecha_creacion' => now(),
                'modificado_por' => 'system',
                'fecha_modificacion' => now(),
            ]);
            $pData['id_usuario_fk'] = $userId;

            if ($dry) {
                $this->line("Would create Persona and pivot for Cliente {$c->id_cliente_pk} ({$tipo})");
                continue;
            }

            $persona = Persona::create($pData);
            $createdPersonas++;
            DB::table('tbl_cliente_persona')->insert([
                'id_cliente_fk' => $c->id_cliente_pk,
                'id_persona_fk' => $persona->id_persona_pk,
            ]);
            $createdPivots++;
        }

        $this->info("Created personas: {$createdPersonas}, created pivots: {$createdPivots}");
        return Command::SUCCESS;
    }
}
