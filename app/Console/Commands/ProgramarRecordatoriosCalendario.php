<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Calendario;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Proyecto;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProgramarRecordatoriosCalendario extends Command
{
    
    protected $signature = 'calendario:programar-recordatorios {--days=2 : Number of days ahead to notify}';

    
    protected $description = 'Enviar notificaciones para eventos de calendario con fecha cercana';

    public function handle()
    {
        $days = (int) $this->option('days');
        $start = Carbon::now();
        $end = Carbon::now()->addDays($days)->endOfDay();

        $this->info("Buscando eventos entre {$start} y {$end}...");

        $events = Calendario::with(['estado', 'cliente'])
            ->whereBetween('fecha', [$start->toDateTimeString(), $end->toDateTimeString()])
            ->get();

        if ($events->isEmpty()) {
            $this->info('No hay eventos próximos.');
            return 0;
        }

        
        $rols = Rol::where('rol', 'like', '%tecn%')->get();
        $roleIds = $rols->pluck('id_rol_pk')->all();
        $userIdsPrimary = Usuario::whereIn('id_rol_fk', $roleIds)->pluck('id_usuario_pk')->all();
        $userIdsPivot = \Illuminate\Support\Facades\DB::table('tbl_usuario_rol')
            ->whereIn('id_rol_fk', $roleIds)
            ->pluck('id_usuario_fk')
            ->all();

        $userIds = collect($userIdsPrimary)->merge($userIdsPivot)->unique()->values()->all();

        if (empty($userIds)) {
            $this->warn('No se encontraron usuarios técnicos para notificar.');
            return 0;
        }

        $users = Usuario::whereIn('id_usuario_pk', $userIds)->get();

        foreach ($events as $ev) {
            
            try {
                $fechaStr = Carbon::parse($ev->fecha)->format('d/m/Y H:i');
            } catch (\Throwable $t) {
                $fechaStr = (string)$ev->fecha;
            }

            $clienteNombre = $ev->cliente->nombre ?? ($ev->cliente->nombre_comercial ?? 'Cliente');

            $payload = [
                'title' => 'Evento próximo',
                'body' => "Evento: {$ev->descripcion_calendario} — Fecha: {$fechaStr} — Cliente: {$clienteNombre}",
                'url' => '/admin/calendario',
                'icon' => 'fa-calendar',
                'severity' => 'info',
                'module' => 'calendario',
                'meta' => ['id_calendario_pk' => $ev->getKey(), 'fecha' => $ev->fecha]
            ];

            foreach ($users as $u) {
                try {
                    $u->notify(new SystemNotification($payload));
                } catch (\Throwable $e) {
                    Log::warning('Failed to notify user ' . $u->id_usuario_pk . ' for upcoming event ' . $ev->getKey() . ': ' . $e->getMessage());
                }
            }
            $this->info('Notificaciones enviadas para evento ' . $ev->getKey());
        }

        
        $this->info('Buscando proyectos con fecha de inicio entre {$start} y {$end}...');
        $projects = Proyecto::whereBetween('fecha_inicio_proyecto', [$start->toDateString(), $end->toDateString()])
            ->get();

        if ($projects->isEmpty()) {
            $this->info('No hay proyectos con inicio próximo.');
            return 0;
        }

        foreach ($projects as $p) {
            try {
                $fechaStr = Carbon::parse($p->fecha_inicio_proyecto)->format('d/m/Y');
            } catch (\Throwable $t) {
                $fechaStr = (string)$p->fecha_inicio_proyecto;
            }

            $payload = [
                'title' => 'Inicio de proyecto próximo',
                'body' => "Proyecto: {$p->nombre_proyecto} — Inicio: {$fechaStr}",
                'url' => '/admin/proyectos',
                'icon' => 'fa-briefcase',
                'severity' => 'info',
                'module' => 'proyectos',
                'meta' => ['id_proyecto_pk' => $p->getKey(), 'fecha_inicio_proyecto' => $p->fecha_inicio_proyecto]
            ];

            foreach ($users as $u) {
                try {
                    $u->notify(new SystemNotification($payload));
                } catch (\Throwable $e) {
                    Log::warning('Failed to notify user ' . $u->id_usuario_pk . ' for upcoming project ' . $p->getKey() . ': ' . $e->getMessage());
                }
            }
            $this->info('Notificaciones enviadas para proyecto ' . $p->getKey());
        }

        return 0;
    }
}
