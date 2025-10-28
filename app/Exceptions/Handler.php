<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

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

                $statusCode = $e instanceof HttpExceptionInterface ? $e->getStatusCode() : 500;
                $response = [
                    'error' => 'Ocurrió un error en el servidor.',
                    'message' => $e->getMessage(),
                ];
                if (config('app.debug')) {
                    $response['trace'] = $e->getTraceAsString();
                }
                return response()->json($response, $statusCode);
            }
        });
    }
}
