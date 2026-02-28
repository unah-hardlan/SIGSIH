<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DbNotification;
use App\Models\Usuario;
use Illuminate\Http\Request;

class NotificationWebController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['data' => [], 'meta' => ['unread' => 0]]);
        }
        try {
            $items = DbNotification::query()
                ->where('tipo_notificable', Usuario::class)
                ->where('id_notificable', $user->id_usuario_pk)
                ->orderByDesc('fecha_creacion')
                ->limit(20)
                ->get();
            $mapped = $items->map(function ($n) {
                return [
                    'id' => $n->id_notificacion ?? $n->id,
                    'title' => $n->data['title'] ?? '',
                    'body' => $n->data['body'] ?? '',
                    'url' => $n->data['url'] ?? '#',
                    'icon' => $n->data['icon'] ?? 'fa-bell',
                    'severity' => $n->data['severity'] ?? 'info',
                    'module' => $n->data['module'] ?? null,
                    'created_at' => optional($n->fecha_creacion)->toDateTimeString(),
                    'read_at' => optional($n->fecha_lectura)->toDateTimeString(),
                ];
            });
            return response()->json([
                'data' => $mapped,
                'meta' => ['unread' => $items->whereNull('fecha_lectura')->count()],
            ]);
        } catch (\Throwable $e) {
            return response()->json(['data' => [], 'meta' => ['unread' => 0]]);
        }
    }

    public function markAllRead()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['ok' => false], 401);
        }
        try {
            DbNotification::query()
                ->where('tipo_notificable', Usuario::class)
                ->where('id_notificable', $user->id_usuario_pk)
                ->whereNull('fecha_lectura')
                ->update(['fecha_lectura' => now()]);
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false], 500);
        }
    }

    public function markRead($id)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['ok' => false], 401);
        }
        try {
            $n = DbNotification::query()
                ->where('id_notificacion', $id)
                ->where('tipo_notificable', Usuario::class)
                ->where('id_notificable', $user->id_usuario_pk)
                ->first();
            if ($n) {
                $n->markAsRead();
            }
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false], 500);
        }
    }
}
