<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;

class Handler extends ExceptionHandler
{
    protected $levels = [
        //
    ];

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // Personalizar los logs
            $this->logError($e);
        });
    }

    /**
     * Log personalizado para excepciones.
     *
     * @param Throwable $e
     * @return void
     */
    protected function logError(Throwable $e)
    {
        Log::error('Excepción capturada', [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'context' => request()->all(), // Agrega datos relevantes del request
        ]);
    }

    public function render($request, Throwable $exception)
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => $exception->getMessage(),
                'code' => $exception->getCode(),
            ], 500);
        }

        return parent::render($request, $exception);
    }
    public function report(Throwable $exception)
    {
        Log::error('Excepción capturada', [
            'message' => $exception->getMessage(),
            'code' => $exception->getCode(),
            'context' => request()->all(),
        ]);

        // Detener propagación (no llama a parent::report())
        if ($this->shouldntReport($exception)) {
            return;
        }
    }
}
