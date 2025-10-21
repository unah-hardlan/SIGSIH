<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Cliente;

class EnsurePersonasClientes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clientes:ensure-personas {--dry-run : Solo mostrar cuántos se crearían}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea registros en tbl_cliente (tipo persona) y en la pivote tbl_cliente_persona para todas las personas sin mapeo';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $dry = (bool) $this->option('dry-run');

        // Personas sin mapeo en tbl_cliente_persona
        $personas = DB::table('tbl_persona as p')
            ->leftJoin('tbl_cliente_persona as cp', 'cp.id_persona_fk', '=', 'p.id_persona_pk')
            ->whereNull('cp.id_persona_fk')
            ->select('p.id_persona_pk')
            ->get();

        $total = $personas->count();
        if ($total === 0) {
            $this->info('No hay personas pendientes por mapear.');
            return Command::SUCCESS;
        }

        $this->info("Personas sin cliente: {$total}");
        if ($dry) {
            $this->info('Dry-run: no se crearán registros.');
            return Command::SUCCESS;
        }

        $creados = 0;
        DB::beginTransaction();
        try {
            foreach ($personas as $p) {
                // Crear cliente tipo persona y mapear en pivote
                $clienteId = DB::table('tbl_cliente')->insertGetId([
                    'tipo_cliente' => 'persona',
                    'fecha_registro' => now(),
                    'estado_cliente' => 'activo',
                ], 'id_cliente_pk');

                DB::table('tbl_cliente_persona')->insert([
                    'id_cliente_fk' => $clienteId,
                    'id_persona_fk' => $p->id_persona_pk,
                ]);
                $creados++;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Error creando clientes para personas: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->info("Clientes persona creados: {$creados}");
        return Command::SUCCESS;
    }
}
