<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the database connection';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::connection()->getPdo();
            $this->info('Conexión exitosa a la base de datos: ' . DB::connection()->getDatabaseName());
        } catch (\Exception $e) {
            $this->error('Error al conectar con la base de datos: ' . $e->getMessage());
        }

        return 0;
    }
}
