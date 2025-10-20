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
                'id' => $n->id, // accessor maps to id_notificacion
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
}
