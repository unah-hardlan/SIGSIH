<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TestDatabaseConnection extends Command
{
    
    protected $signature = 'db:test';

    
    protected $description = 'Test the database connection';

    
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
