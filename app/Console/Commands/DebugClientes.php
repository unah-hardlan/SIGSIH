<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;

class DebugClientes extends Command
{
    protected $signature = 'clientes:debug {--limit=10 : Show up to N sample clientes with persona mapping}';
    protected $description = 'Print diagnostic info about clientes/personas/empresas and their mappings';
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $limit = $limit > 0 ? $limit : 10;

        $counts = [
            'tbl_cliente' => DB::table('tbl_cliente')->count(),
            'clientes_persona' => DB::table('tbl_cliente')->where('tipo_cliente', 'persona')->count(),
            'clientes_empresa' => DB::table('tbl_cliente')->where('tipo_cliente', 'empresa')->count(),
            'tbl_persona' => DB::table('tbl_persona')->count(),
            'tbl_cliente_empresa' => DB::table('tbl_cliente_empresa')->count(),
            'tbl_cliente_persona' => DB::table('tbl_cliente_persona')->count(),
        ];

        $this->info('Counts:');
        $this->line(json_encode($counts, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->newLine();
        $this->info("Sample clientes with relations (limit {$limit}):");
        $sample = Cliente::query()->with(['empresa', 'personas'])->limit($limit)->get();
        $mapped = $sample->map(function ($c) {
            $tipo = is_string($c->tipo_cliente) ? strtolower($c->tipo_cliente) : $c->tipo_cliente;
            $persona = $c->personas->first();
            return [
                'id_cliente_pk' => $c->id_cliente_pk,
                'tipo' => $tipo,
                'empresa' => $c->empresa ? [
                    'nombre_comercial' => $c->empresa->nombre_comercial,
                    'razon_social' => $c->empresa->razon_social,
                    'rtn' => $c->empresa->rtn,
                ] : null,
                'persona_id' => $persona->id_persona_pk ?? null,
                'persona_nombre' => $persona ? trim(($persona->primer_nombre ?? '') . ' ' . ($persona->primer_apellido ?? '')) : null,
                'has_pivot' => DB::table('tbl_cliente_persona')->where('id_cliente_fk', $c->id_cliente_pk)->exists(),
            ];
        });
        $this->line(json_encode($mapped, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->newLine();
        $this->info('Empresas sin persona mapeada:');
        $empSinPersona = Cliente::query()
            ->where('tipo_cliente', 'empresa')
            ->whereDoesntHave('personas')
            ->pluck('id_cliente_pk');
        $this->line($empSinPersona->isEmpty() ? '(ninguna)' : $empSinPersona->implode(', '));

        return Command::SUCCESS;
    }
}