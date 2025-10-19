<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GestionDbController extends Controller
{
    /**
     * Garantiza que el texto sea UTF-8 válido para respuestas JSON.
     */
    private function ensureUtf8(?string $text): string
    {
        if ($text === null) return '';
        // Si ya es UTF-8 válido, devolver tal cual
        if (function_exists('mb_detect_encoding') && @mb_detect_encoding($text, 'UTF-8', true)) {
            return $text;
        }
        // Intentar convertir automáticamente
        if (function_exists('mb_convert_encoding')) {
            $conv = @mb_convert_encoding($text, 'UTF-8', 'auto');
            if ($conv !== false) return $conv;
        }
        // Fallback común en Windows (CP1252)
        $conv = @iconv('CP1252', 'UTF-8//IGNORE', $text);
        if ($conv !== false) return $conv;
        // Último recurso: filtrar bytes no válidos
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? '';
    }

    /**
     * Elimina comillas envolventes ("..." o '...') de rutas provenientes del .env.
     */
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
            // Para MySQL permitimos omitir path y generamos en storage/app/backups
            if ($driver !== 'mysql') {
                return response()->json(['error' => 'Debe proporcionar la ruta de destino (path).'], 422);
            }
        }

        // Verificación de seguridad: solicitar la contraseña de la BD definida en .env
        $expected = '';
        if ($driver === 'mysql') {
            $expected = (string) (config('database.connections.mysql.password') ?? '');
        }
        $provided = $request->has('confirm_password') ? (string)$request->input('confirm_password') : null;
        if ($expected === '') {
            // Si la BD no tiene contraseña en .env, permitir confirmación vacía o no enviada
            if ($provided === null) {
                $provided = '';
            }
        } else {
            // Si hay contraseña configurada, exigir que el usuario la envíe (respuesta suave para evitar 422 en consola)
            if ($provided === null || $provided === '') {
                return response()->json([
                    'ok' => false,
                    'code' => 'MISSING_CONFIRM_PASSWORD',
                    'error' => 'Debe ingresar la contraseña para confirmar el respaldo.',
                    'errors' => ['confirm_password' => ['Debe ingresar la contraseña para confirmar el respaldo.']],
                ], 200);
            }
        }
        // Comparación segura
        if (!hash_equals($expected, (string)$provided)) {
            // Evitar ruido en consola (422): devolvemos 200 con error estructurado para manejo en UI
            return response()->json([
                'ok' => false,
                'code' => 'INVALID_CONFIRM_PASSWORD',
                'error' => 'Contraseña de verificación incorrecta.',
                'errors' => ['confirm_password' => ['Contraseña de verificación incorrecta.']],
            ], 200);
        }

        try {
            if ($driver !== 'mysql') {
                return response()->json(['error' => 'Este módulo de respaldo está configurado solo para MySQL.'], 400);
            }
            return $this->backupMySql($path, $request->input('mysqldump_path', 'mysqldump'));
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'Error realizando respaldo',
                'message' => $this->ensureUtf8($e->getMessage()),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
    }

    protected function backupMySql(?string $path, string $mysqldump = 'mysqldump')
    {
        $conf = config('database.connections.mysql');
        $host = $conf['host'] ?? '127.0.0.1';
        $port = (string)($conf['port'] ?? '3306');
        $db = $conf['database'] ?? '';
        $user = $conf['username'] ?? '';
        $pass = $conf['password'] ?? '';

        if (!$db) {
            return response()->json(['error' => 'Base de datos no configurada.'], 500);
        }

        // Siempre generamos el dump en storage/app/backups para poder descargarlo por HTTP
        $storageDir = storage_path('app/backups');
        if (!is_dir($storageDir)) {
            if (!@mkdir($storageDir, 0775, true) && !is_dir($storageDir)) {
                return response()->json(['error' => 'No se pudo crear el directorio de respaldos: ' . $storageDir], 500);
            }
        }
        $timestamp = date('Ymd-His');
        $filename = sprintf('%s-%s.sql', $db ?: 'backup', $timestamp);
        $storagePath = $storageDir . DIRECTORY_SEPARATOR . $filename;

        // Resolver ruta de mysqldump: env override o detección común en Windows
        $envDump = $this->unquote(env('MYSQLDUMP_PATH'));
        if ($envDump && is_string($envDump)) {
            $mysqldump = $envDump;
        } else {
            $candidates = [
                'mysqldump',
                'C:\\xampp\\mysql\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\mysqldump.exe',
                'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
                'C:\\Program Files (x86)\\MySQL\\MySQL Server 5.7\\bin\\mysqldump.exe',
                'C:\\wamp64\\bin\\mysql\\mysql8.0.31\\bin\\mysqldump.exe',
            ];
            foreach ($candidates as $cand) {
                if (stripos(PHP_OS, 'WIN') === 0) {
                    if (@file_exists($cand)) {
                        $mysqldump = $cand;
                        break;
                    }
                } else {
                    // En Linux/Mac confiar en PATH o rutas absolutas si las hubiera
                }
            }
        }

        // Aconsejar extensión .sql si es .bak
        // No obligatorio: se permite cualquier extensión

        // Detectar plugin-dir si se requiere (Windows + MySQL 8: caching_sha2_password.dll)
        $pluginDir = $this->unquote(env('MYSQL_PLUGIN_DIR'));
        if (!$pluginDir && stripos(PHP_OS, 'WIN') === 0) {
            // Si mysqldump está en ...\bin\mysqldump.exe intentar ..\lib\plugin
            $binDir = @dirname($mysqldump);
            if ($binDir && preg_match('/\\\\bin$/i', $binDir)) {
                $candidate = preg_replace('/\\\\bin$/i', '\\\\lib\\\\plugin', $binDir);
                if ($candidate && @is_dir($candidate)) {
                    $pluginDir = $candidate;
                }
            }
            // XAMPP estructura típica: C:\xampp\mysql\bin -> C:\xampp\mysql\lib\plugin
            if (!$pluginDir && $binDir) {
                $cand = str_replace('\\\\bin', '\\\\lib\\\\plugin', $binDir);
                if ($cand && @is_dir($cand)) {
                    $pluginDir = $cand;
                }
            }
        }

        // Ejecutar mysqldump escribiendo directamente al archivo (--result-file) para evitar usar memoria PHP
        $args = [];
        $args[] = $mysqldump;
        $args[] = "--host={$host}";
        $args[] = "--port={$port}";
        $args[] = "--user={$user}";
        if ($pass !== '') {
            $args[] = "--password={$pass}";
        }
        if ($pluginDir) {
            $args[] = "--plugin-dir={$pluginDir}";
        }
        $args[] = '--routines';
        $args[] = '--events';
        $args[] = '--single-transaction';
        $args[] = '--quick';
        $args[] = '--hex-blob';
        $args[] = "--result-file={$storagePath}";
        $args[] = $db;

        // Construir comando seguro (sin redirección; capturamos stdout)
        $cmd = '';
        foreach ($args as $a) {
            // Escapar con comillas dobles para Windows; password ya va inline por flag
            if (preg_match('/\s/', $a)) {
                $cmd .= ' "' . $a . '"';
            } else {
                $cmd .= ' ' . $a;
            }
        }
        $cmd = ltrim($cmd);

        $descriptorspec = [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']];
        $proc = @proc_open($cmd, $descriptorspec, $pipes, null, null);
        if (!\is_resource($proc)) {
            return response()->json(['error' => 'No se pudo iniciar mysqldump. Configure MYSQLDUMP_PATH en el .env (por ejemplo C:\\xampp\\mysql\\bin\\mysqldump.exe) o agregue mysqldump al PATH del sistema.'], 500);
        }
        fclose($pipes[0]);
        $stdout = stream_get_contents($pipes[1]) ?: '';
        $stderr = stream_get_contents($pipes[2]) ?: '';
        fclose($pipes[1]);
        fclose($pipes[2]);
        $exit = proc_close($proc);

        if ($exit !== 0) {
            $msg = trim($stderr) ?: trim($stdout) ?: 'mysqldump falló';
            // Mensaje común de Windows cuando no está en PATH
            if (stripos($msg, 'no se reconoce') !== false || stripos($msg, 'not recognized') !== false) {
                $msg .= '. Configure MYSQLDUMP_PATH en .env (e.g. C:\\xampp\\mysql\\bin\\mysqldump.exe) y ejecute php artisan config:clear.';
            }
            return response()->json([
                'error' => 'mysqldump falló',
                'message' => $this->ensureUtf8($msg),
            ], 500, [], JSON_UNESCAPED_UNICODE);
        }
        // Validar que el archivo se haya creado
        if (!@file_exists($storagePath)) {
            return response()->json(['error' => 'El archivo de respaldo no fue creado. Verifique permisos o la ruta.'], 500);
        }

        // Si el usuario proporcionó un path destino, intentar copiar como conveniencia
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

        return response()->json([
            'message' => 'Respaldo creado correctamente',
            'path' => $finalPath,
            'size' => @filesize($finalPath) ?: null,
            'driver' => 'mysql',
            'download_url' => $downloadUrl,
            'copied_to_requested_path' => $copied,
            'copy_error' => $copyError,
        ], 200, [], JSON_UNESCAPED_UNICODE);
    }

    // SQL Server soporte removido para simplificar a MySQL únicamente.

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
}
