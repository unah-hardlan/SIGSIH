<?php

namespace App\Services;

use App\Models\RespaldoBd;
use Illuminate\Support\Collection;

class DatabaseBackupService
{
    private int $maxBackups = 10;

    public function maxBackups(): int
    {
        return $this->maxBackups;
    }

    public function activeBackupsCount(): int
    {
        return RespaldoBd::where('estado_respaldo', 'ACTIVO')->count();
    }

    public function oldestActiveBackup(): ?RespaldoBd
    {
        return RespaldoBd::where('estado_respaldo', 'ACTIVO')
            ->orderBy('fecha_creacion', 'asc')
            ->first();
    }

    public function listRecentActive(int $limit = 10): Collection
    {
        return RespaldoBd::where('estado_respaldo', 'ACTIVO')
            ->orderByDesc('fecha_creacion')
            ->limit($limit)
            ->get();
    }

    public function createBackup(string $type = 'manual', ?int $userId = null, ?string $createdBy = null): array
    {
        $conf = config('database.connections.mysql');
        $host = (string) ($conf['host'] ?? '127.0.0.1');
        $port = (string) ($conf['port'] ?? '3306');
        $db = (string) ($conf['database'] ?? '');
        $user = (string) ($conf['username'] ?? '');
        $pass = (string) ($conf['password'] ?? '');

        if ($db === '') {
            throw new \RuntimeException('Base de datos no configurada.');
        }

        $storageDir = storage_path('app/backups');
        if (!is_dir($storageDir) && !@mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
            throw new \RuntimeException('No se pudo crear el directorio de respaldos.');
        }

        $timestamp = date('Ymd-His');
        $filename = sprintf('%s-%s.sql', $db, $timestamp);
        $storagePath = $storageDir . DIRECTORY_SEPARATOR . $filename;

        $mysqldump = $this->resolveMySqlDumpBinary();
        $pluginDir = $this->resolvePluginDir($mysqldump);

        $args = [
            $mysqldump,
            "--host={$host}",
            "--port={$port}",
            "--user={$user}",
            '--routines',
            '--events',
            '--single-transaction',
            '--quick',
            '--hex-blob',
            "--result-file={$storagePath}",
            $db,
        ];

        if ($pass !== '') {
            $args[] = "--password={$pass}";
        }
        if ($pluginDir !== null) {
            $args[] = "--plugin-dir={$pluginDir}";
        }

        $result = $this->runCommand($args);
        if ($result['exit_code'] !== 0) {
            throw new \RuntimeException($this->normalizeErrorMessage($result['stderr'] ?: $result['stdout'] ?: 'mysqldump fallo'));
        }

        if (!@file_exists($storagePath)) {
            throw new \RuntimeException('El archivo de respaldo no fue creado.');
        }

        $size = @filesize($storagePath) ?: null;
        $sha1 = @sha1_file($storagePath) ?: null;

        $backup = RespaldoBd::create([
            'nombre_archivo' => $filename,
            'ruta_archivo' => $storagePath,
            'tamano_bytes' => $size,
            'checksum_sha1' => $sha1,
            'tipo_respaldo' => in_array($type, ['manual', 'automatico'], true) ? $type : 'manual',
            'estado_respaldo' => 'ACTIVO',
            'id_usuario_fk' => $userId,
            'observacion' => null,
            'creado_por' => $createdBy ?: 'system',
            'fecha_creacion' => now(),
            'modificado_por' => $createdBy ?: 'system',
            'fecha_modificacion' => now(),
        ]);

        $rotation = $this->enforceMaxBackups($createdBy ?: 'system');

        return [
            'backup' => $backup,
            'path' => $storagePath,
            'filename' => $filename,
            'size' => $size,
            'rotation' => $rotation,
        ];
    }

    public function deleteBackup(RespaldoBd $backup, string $updatedBy = 'system'): void
    {
        $this->deleteBackupFileIfExists((string) $backup->ruta_archivo);

        $backup->estado_respaldo = 'ELIMINADO';
        $backup->observacion = 'Eliminado manualmente';
        $backup->modificado_por = $updatedBy;
        $backup->fecha_modificacion = now();
        $backup->save();
    }

    public function restoreBackup(RespaldoBd $backup): void
    {
        $path = (string) $backup->ruta_archivo;
        if (!@file_exists($path)) {
            throw new \RuntimeException('El archivo de respaldo no existe en disco.');
        }

        $conf = config('database.connections.mysql');
        $host = (string) ($conf['host'] ?? '127.0.0.1');
        $port = (string) ($conf['port'] ?? '3306');
        $db = (string) ($conf['database'] ?? '');
        $user = (string) ($conf['username'] ?? '');
        $pass = (string) ($conf['password'] ?? '');

        if ($db === '') {
            throw new \RuntimeException('Base de datos no configurada.');
        }

        $mysql = $this->resolveMySqlBinary();
        $args = [
            $mysql,
            "--host={$host}",
            "--port={$port}",
            "--user={$user}",
            $db,
        ];
        if ($pass !== '') {
            $args[] = "--password={$pass}";
        }

        $cmd = $this->buildCommandString($args);
        $desc = [0 => ['pipe', 'w'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $proc = @proc_open($cmd, $desc, $pipes);
        if (!is_resource($proc)) {
            throw new \RuntimeException('No se pudo iniciar el cliente mysql para restaurar.');
        }

        $source = @fopen($path, 'rb');
        if (!is_resource($source)) {
            @fclose($pipes[0]);
            @fclose($pipes[1]);
            @fclose($pipes[2]);
            @proc_close($proc);
            throw new \RuntimeException('No se pudo abrir el archivo de respaldo.');
        }

        stream_copy_to_stream($source, $pipes[0]);
        fclose($source);
        fclose($pipes[0]);

        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exit = proc_close($proc);
        if ($exit !== 0) {
            throw new \RuntimeException($this->normalizeErrorMessage($stderr ?: $stdout ?: 'La restauracion fallo.'));
        }
    }

    private function enforceMaxBackups(string $updatedBy): array
    {
        $toDelete = RespaldoBd::where('estado_respaldo', 'ACTIVO')
            ->orderByDesc('fecha_creacion')
            ->skip($this->maxBackups)
            ->take(50)
            ->get();

        if ($toDelete->isEmpty()) {
            return ['deleted_count' => 0, 'deleted_names' => []];
        }

        $deletedNames = [];
        foreach ($toDelete as $item) {
            $this->deleteBackupFileIfExists((string) $item->ruta_archivo);
            $item->estado_respaldo = 'ELIMINADO';
            $item->observacion = 'Eliminado por politica de retencion (maximo 10)';
            $item->modificado_por = $updatedBy;
            $item->fecha_modificacion = now();
            $item->save();
            $deletedNames[] = $item->nombre_archivo;
        }

        return [
            'deleted_count' => count($deletedNames),
            'deleted_names' => $deletedNames,
        ];
    }

    private function deleteBackupFileIfExists(string $path): void
    {
        if ($path !== '' && @file_exists($path)) {
            @unlink($path);
        }
    }

    private function resolveMySqlDumpBinary(): string
    {
        $envDump = $this->unquote(env('MYSQLDUMP_PATH'));
        if ($envDump && @file_exists($envDump)) {
            return $envDump;
        }

        $candidates = [
            'mysqldump',
            'C:\\xampp\\mysql\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
        ];

        foreach ($candidates as $candidate) {
            if (stripos(PHP_OS, 'WIN') === 0) {
                if (@file_exists($candidate)) return $candidate;
            } else {
                if ($candidate === 'mysqldump') return $candidate;
            }
        }

        return 'mysqldump';
    }

    private function resolveMySqlBinary(): string
    {
        $envMysql = $this->unquote(env('MYSQL_PATH'));
        if ($envMysql && @file_exists($envMysql)) {
            return $envMysql;
        }

        $candidates = [
            'mysql',
            'C:\\xampp\\mysql\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysql.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysql.exe',
            'C:\\Program Files (x86)\\MySQL\\MySQL Server 5.7\\bin\\mysql.exe',
        ];

        foreach ($candidates as $candidate) {
            if (stripos(PHP_OS, 'WIN') === 0) {
                if (@file_exists($candidate)) return $candidate;
            } else {
                if ($candidate === 'mysql') return $candidate;
            }
        }

        return 'mysql';
    }

    private function resolvePluginDir(string $mysqldump): ?string
    {
        $pluginDir = $this->unquote(env('MYSQL_PLUGIN_DIR'));
        if ($pluginDir && @is_dir($pluginDir)) {
            return $pluginDir;
        }

        if (stripos(PHP_OS, 'WIN') !== 0) {
            return null;
        }

        $binDir = @dirname($mysqldump);
        if (!$binDir) return null;

        $candidate = preg_replace('/\\\\bin$/i', '\\\\lib\\\\plugin', $binDir);
        if (is_string($candidate) && @is_dir($candidate)) {
            return $candidate;
        }

        $fallback = str_replace('\\\\bin', '\\\\lib\\\\plugin', $binDir);
        if (@is_dir($fallback)) {
            return $fallback;
        }

        return null;
    }

    private function runCommand(array $args): array
    {
        $cmd = $this->buildCommandString($args);

        $descriptors = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $proc = @proc_open($cmd, $descriptors, $pipes);
        if (!is_resource($proc)) {
            return ['exit_code' => 1, 'stdout' => '', 'stderr' => 'No se pudo iniciar el proceso.'];
        }

        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);

        $exit = proc_close($proc);
        return ['exit_code' => $exit, 'stdout' => $stdout, 'stderr' => $stderr];
    }

    private function buildCommandString(array $args): string
    {
        $cmd = '';
        foreach ($args as $arg) {
            $arg = (string) $arg;
            if (preg_match('/\s/', $arg)) {
                $cmd .= ' "' . $arg . '"';
            } else {
                $cmd .= ' ' . $arg;
            }
        }
        return ltrim($cmd);
    }

    private function normalizeErrorMessage(?string $message): string
    {
        $text = $message ?? '';
        if ($text === '') {
            return 'No se pudo completar la operacion.';
        }

        if (!mb_detect_encoding($text, 'UTF-8', true)) {
            $text = @mb_convert_encoding($text, 'UTF-8', 'auto') ?: $text;
        }

        return trim($text);
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
}
