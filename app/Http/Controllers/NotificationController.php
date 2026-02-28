<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $limit = (int) $request->query('limit', 15);
        $items = $user->notifications()->limit($limit)->get()->map(function ($n) {
            return [
                'id' => $n->id,
                'title' => $n->data['title'] ?? 'Notificación',
                'body' => $n->data['body'] ?? '',
                'url' => $n->data['url'] ?? null,
                'icon' => $n->data['icon'] ?? 'fa-bell',
                'severity' => $n->data['severity'] ?? 'info',
                'module' => $n->data['module'] ?? null,
                'meta' => $n->data['meta'] ?? [],
                'read_at' => optional($n->read_at)->toISOString(),
                'created_at' => optional($n->fecha_creacion)->toISOString(),
            ];
        });

        $countUnread = $user->unreadNotifications()->count();
        return response()->json(['data' => $items, 'unread' => $countUnread]);
    }


    public function webIndex(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['data' => [], 'meta' => ['unread' => 0]]);
        }
        try {
            $items = \App\Models\DbNotification::query()
                ->where('tipo_notificable', \App\Models\Usuario::class)
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

    public function webMarkAllRead(): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['ok' => false], 401);
        }
        try {
            \App\Models\DbNotification::query()
                ->where('tipo_notificable', \App\Models\Usuario::class)
                ->where('id_notificable', $user->id_usuario_pk)
                ->whereNull('fecha_lectura')
                ->update(['fecha_lectura' => now()]);
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false], 500);
        }
    }

    public function webMarkRead($id): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['ok' => false], 401);
        }
        try {
            $n = \App\Models\DbNotification::query()
                ->where('id_notificacion', $id)
                ->where('tipo_notificable', \App\Models\Usuario::class)
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

    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $n = $request->user()->notifications()->where('id_notificacion', $id)->first();
        if (!$n) {
            return response()->json(['error' => 'Not found'], 404);
        }
        if (!$n->read_at) {
            $n->markAsRead();
        }
        return response()->json(['ok' => true]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();
        return response()->json(['ok' => true]);
    }


    public function destroy(Request $request, string $id): JsonResponse
    {
        $n = $request->user()->notifications()->where('id_notificacion', $id)->first();
        if (!$n) {
            return response()->json(['error' => 'Not found'], 404);
        }
        try {
            $n->delete();
            return response()->json(['ok' => true]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to delete', 'message' => $e->getMessage()], 500);
        }
    }
}
