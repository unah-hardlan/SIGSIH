<?php

namespace App\Observers;

use App\Models\Factura;
use App\Models\EstadoFactura;
use App\Notifications\SystemNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class FacturaObserver
{
    public function created(Factura $factura): void
    {
        $this->notifyCliente($factura, [
            'title' => 'Factura creada',
            'body' => sprintf('Se ha creado la factura %s por un total de L. %s.', (string)($factura->numero ?? $factura->id_factura_pk), number_format((float)($factura->total ?? 0), 2)),
            'url' => $this->clienteFacturasUrl(),
            'icon' => 'fa-file-invoice',
            'severity' => 'info',
            'module' => 'Facturación',
            'meta' => [
                'factura_id' => $factura->getKey(),
                'numero' => $factura->numero,
                'total' => $factura->total,
            ],
        ]);
    }

    public function updated(Factura $factura): void
    {
        if ($factura->wasChanged('id_estado_factura_fk')) {
            $oldId = $factura->getOriginal('id_estado_factura_fk');
            $newId = $factura->id_estado_factura_fk;

            $old = $oldId ? EstadoFactura::find($oldId) : null;
            $new = $newId ? EstadoFactura::find($newId) : null;

            $this->notifyCliente($factura, [
                'title' => 'Estado de factura actualizado',
                'body' => sprintf(
                    'La factura %s cambió de estado: %s → %s.',
                    (string)($factura->numero ?? $factura->id_factura_pk),
                    $old->nombre ?? 'N/D',
                    $new->nombre ?? 'N/D'
                ),
                'url' => $this->clienteFacturasUrl(),
                'icon' => 'fa-arrows-rotate',
                'severity' => 'warning',
                'module' => 'Facturación',
                'meta' => [
                    'factura_id' => $factura->getKey(),
                    'numero' => $factura->numero,
                    'estado_anterior' => $old?->nombre,
                    'estado_nuevo' => $new?->nombre,
                ],
            ]);
        }
    }

    /**
     * Envía notificación a los usuarios vinculados al cliente de la factura.
     * Se buscan usuarios a través de las personas asociadas al cliente.
     */
    protected function notifyCliente(Factura $factura, array $payload): void
    {
        try {
            $cliente = $factura->cliente()->with(['personas.usuario'])->first();
            if (!$cliente) {
                return;
            }

            $usuarios = $cliente->personas
                ->map(fn($p) => $p->usuario)
                ->filter()
                ->unique('id_usuario_pk')
                ->values();

            if ($usuarios->isEmpty()) {
                // Sin usuarios asociados; no enviamos nada por ahora.
                return;
            }

            Notification::send($usuarios, new SystemNotification($payload));
        } catch (\Throwable $e) {
            // Evitar que una excepción en notificaciones rompa el flujo de guardado
            Log::warning('FacturaObserver notifyCliente failed', [
                'error' => $e->getMessage(),
                'factura_id' => $factura->getKey(),
            ]);
        }
    }

    protected function clienteFacturasUrl(): ?string
    {
        try {
            return route('cliente.facturas');
        } catch (\Throwable $e) {
            return null;
        }
    }
}
