<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Database\QueryException;
use PDOException;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return response()->json([
                        'message' => 'Hay información incorrecta. Verifica los datos e inténtalo de nuevo.',
                        'errors' => $e->errors(),
                    ], 422);
                }

                // Sanitizar errores de base de datos para evitar exponer nombres de tablas/columnas
                if ($e instanceof QueryException || $e instanceof PDOException) {
                    $sqlState = null;
                    // QueryException tiene errorInfo; PDOException también puede tenerlo
                    if (property_exists($e, 'errorInfo') && is_array($e->errorInfo ?? null)) {
                        $sqlState = (string)($e->errorInfo[0] ?? '');
                    }
                    if (!$sqlState) {
                        $sqlState = (string)$e->getCode();
                    }

                    $isIntegrity = str_starts_with((string)$sqlState, '23'); // e.g., 23000
                    $status = $isIntegrity ? 409 : 500;
                    $message = $isIntegrity
                        ? 'No se pudo completar la operación por una restricción de integridad.'
                        : 'Error al procesar la solicitud.';

                    if (config('app.debug') && $sqlState) {
                        $message .= " (SQLSTATE: {$sqlState})";
                    }

                    return response()->json([
                        'message' => $message,
                    ], $status);
                }
                // Respuesta genérica para otras excepciones en API
                $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
                $genericMessage = $statusCode === 404
                    ? 'Recurso no encontrado.'
                    : 'Ocurrió un error inesperado.';
                $response = [
                    'message' => $genericMessage,
                ];
                if (config('app.debug')) {
                    $response['exception'] = get_class($e);
                    $response['trace'] = $e->getTraceAsString();
                }
                return response()->json($response, $statusCode);
            }
        });
    }
}
