<?php

namespace App\Http\Controllers;

use App\Models\RespaldoBd;
use App\Services\DatabaseBackupService;
use Illuminate\Http\Request;

class GestionDbController extends Controller
{
    public function __construct(private DatabaseBackupService $backupService) {}


    private function ensureUtf8(?string $text): string
    {
        if ($text === null) return '';

        if (function_exists('mb_detect_encoding') && @mb_detect_encoding($text, 'UTF-8', true)) {
            return $text;
        }

        if (function_exists('mb_convert_encoding')) {
            $conv = @mb_convert_encoding($text, 'UTF-8', 'auto');
            if ($conv !== false) return $conv;
        }

        $conv = @iconv('CP1252', 'UTF-8//IGNORE', $text);
        if ($conv !== false) return $conv;

        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? '';
    }


    private function unquote(?string $value): ?string
    {
        if ($value === null) return null;
        $v = trim($value);
        if ((str_starts_with($v, '"') && str_ends_with($v, '"')) || (str_starts_with($v, "'") && str_ends_with($v, "'"))) {
            return substr($v, 1, -1);
        }
        return $v;
    }

    public function backup(Request $request)
    {
        $driver = config('database.default');
        $path = $request->input('path');
        if (!$path) {

            if ($driver !== 'mysql') {
                return response()->json(['error' => 'Debe proporcionar la ruta de destino (path).'], 422);
            }
        }


        if ($passwordError = $this->validateConfirmPassword($request, 'respaldo')) {
            return $passwordError;
        }

        try {
            if ($driver !== 'mysql') {
                return response()->json(['error' => 'Este módulo de respaldo está configurado solo para MySQL.'], 400);
            }
            return $this->backupMySql($path);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error realizando respaldo',
                'message' => config('app.debug') ? $this->safeMessage($e) : 'No se pudo completar el respaldo.',
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    protected function backupMySql(?string $path)
    {
        $actor = auth()->user();
        $userId = (int) ($actor->id_usuario_pk ?? $actor->id ?? 0) ?: null;
        $createdBy = (string) ($actor->usuario ?? 'system');

        $beforeCount = $this->backupService->activeBackupsCount();
        $oldest = $this->backupService->oldestActiveBackup();

        $result = $this->backupService->createBackup('manual', $userId, $createdBy);
        $storagePath = (string) ($result['path'] ?? '');
        $filename = (string) ($result['filename'] ?? basename($storagePath));


        $copied = false;
        $copyError = null;
        $finalPath = $storagePath;
        if ($path && is_string($path)) {
            $destDir = dirname($path);
            if (!is_dir($destDir)) {
                @mkdir($destDir, 0775, true);
            }
            if (@copy($storagePath, $path)) {
                $copied = true;
                $finalPath = $path;
            } else {
                $copyError = 'No se pudo copiar al destino solicitado. Verifique permisos.';
            }
        }

        $downloadUrl = route('db.backup.download', ['file' => $filename]);
        $max = $this->backupService->maxBackups();
        $willDeleteOldestOnNext = $beforeCount >= $max;
        $rotation = $result['rotation'] ?? ['deleted_count' => 0, 'deleted_names' => []];

        return response()->json([
            'message' => 'Respaldo creado correctamente',
            'path' => $finalPath,
            'size' => @filesize($finalPath) ?: null,
            'driver' => 'mysql',
            'download_url' => $downloadUrl,
            'copied_to_requested_path' => $copied,
            'copy_error' => $copyError,
            'max_backups' => $max,
            'current_backups' => $this->backupService->activeBackupsCount(),
            'will_delete_oldest_on_next' => $willDeleteOldestOnNext,
            'oldest_backup_name' => $oldest?->nombre_archivo,
            'rotation_deleted_count' => (int) ($rotation['deleted_count'] ?? 0),
            'rotation_deleted_names' => $rotation['deleted_names'] ?? [],
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    public function listBackups(): \Illuminate\Http\JsonResponse
    {
        $items = $this->backupService->listRecentActive(10)
            ->map(function (RespaldoBd $item) {
                return [
                    'id' => $item->id_respaldo_bd_pk,
                    'nombre_archivo' => $item->nombre_archivo,
                    'tamano_bytes' => $item->tamano_bytes,
                    'tamano_humano' => $this->humanBytes((int) ($item->tamano_bytes ?? 0)),
                    'tipo_respaldo' => $item->tipo_respaldo,
                    'fecha_respaldo' => optional($item->fecha_creacion)->toDateTimeString(),
                    'creado_por' => $item->creado_por,
                    'download_url' => route('db.backup.download', ['file' => $item->nombre_archivo]),
                    'archivo_disponible' => @file_exists((string) $item->ruta_archivo),
                ];
            })
            ->values();

        $max = $this->backupService->maxBackups();
        $count = $this->backupService->activeBackupsCount();
        $oldest = $this->backupService->oldestActiveBackup();

        return response()->json([
            'data' => $items,
            'meta' => [
                'max_backups' => $max,
                'total_activos' => $count,
                'will_delete_oldest_on_next' => $count >= $max,
                'oldest_backup_name' => $oldest?->nombre_archivo,
            ],
        ]);
    }

    public function destroyBackup(int $id): \Illuminate\Http\JsonResponse
    {
        $backup = RespaldoBd::where('estado_respaldo', 'ACTIVO')->find($id);
        if (!$backup) {
            return response()->json(['error' => 'Respaldo no encontrado'], 404);
        }

        $actor = auth()->user();
        $updatedBy = (string) ($actor->usuario ?? 'system');
        $this->backupService->deleteBackup($backup, $updatedBy);

        return response()->json(['message' => 'Respaldo eliminado correctamente']);
    }

    public function restoreBackup(Request $request, int $id): \Illuminate\Http\JsonResponse
    {
        if ($passwordError = $this->validateConfirmPassword($request, 'restauración')) {
            return $passwordError;
        }

        $backup = RespaldoBd::where('estado_respaldo', 'ACTIVO')->find($id);
        if (!$backup) {
            return response()->json(['error' => 'Respaldo no encontrado'], 404);
        }

        try {
            $this->backupService->restoreBackup($backup);
            return response()->json([
                'message' => 'Base de datos restaurada correctamente desde el respaldo seleccionado.'
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No se pudo restaurar la base de datos',
                'message' => config('app.debug') ? $this->safeMessage($e) : 'No se pudo completar la restauración.',
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }



    public function download(Request $request)
    {
        $file = basename((string)$request->query('file'));
        if ($file === '' || strpos($file, '..') !== false) {
            return response()->json(['error' => 'Parámetro de archivo inválido.'], 422);
        }
        $full = storage_path('app/backups/' . $file);
        if (!@file_exists($full)) {
            return response()->json(['error' => 'Archivo no encontrado.'], 404);
        }
        $mime = 'application/sql';
        return response()->download($full, $file, [
            'Content-Type' => $mime,
        ]);
    }


    private function safeMessage(\Throwable $e): string
    {
        $msg = $this->ensureUtf8($e->getMessage());

        $msg = preg_replace('/`[^`]+`/', '`?`', $msg) ?? $msg;

        $patterns = [
            '/\b(table|column|constraint|index)\s+[`\"\[]?[a-z0-9_\.]+[`\"\]]?/i',
            '/\bfor\s+key\s+[`\"\[]?[a-z0-9_\.]+[`\"\]]?/i',
            '/\bforeign\s+key\s*\([^)]+\)/i',
        ];
        foreach ($patterns as $p) {
            $msg = preg_replace($p, '$1 ?', $msg) ?? $msg;
        }
        return $msg;
    }

    private function validateConfirmPassword(Request $request, string $operation)
    {
        $expected = (string) (config('database.connections.mysql.password') ?? '');
        $provided = $request->has('confirm_password') ? (string)$request->input('confirm_password') : null;

        if ($expected === '') {
            if ($provided === null) {
                $provided = '';
            }
        } else {
            if ($provided === null || $provided === '') {
                return response()->json([
                    'ok' => false,
                    'code' => 'MISSING_CONFIRM_PASSWORD',
                    'error' => "Debe ingresar la contraseña para confirmar la {$operation}.",
                    'errors' => ['confirm_password' => ["Debe ingresar la contraseña para confirmar la {$operation}."]],
                ], 200);
            }
        }

        if (!hash_equals($expected, (string)$provided)) {
            return response()->json([
                'ok' => false,
                'code' => 'INVALID_CONFIRM_PASSWORD',
                'error' => 'Contraseña de verificación incorrecta.',
                'errors' => ['confirm_password' => ['Contraseña de verificación incorrecta.']],
            ], 200);
        }

        return null;
    }

    private function humanBytes(int $bytes): string
    {
        if ($bytes <= 0) return '0 B';
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $pow = min((int) floor(log($bytes, 1024)), count($units) - 1);
        $value = $bytes / (1024 ** $pow);
        return number_format($value, $pow === 0 ? 0 : 2) . ' ' . $units[$pow];
    }
}
