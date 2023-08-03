<?php

namespace App\ERP\Exception;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class InvalidOrderException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        Log::error('Se ha producido una excepción de orden inválida: ' . $this->getMessage());
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        return response()->json([
            'error' => 'Orden inválida',
            'message' => $this->getMessage(),
        ], 400);
    }
}
