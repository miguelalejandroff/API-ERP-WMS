<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\CancelarDocumentoService;
use App\ERP\Handler\InfAjustesHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;

class InfAjustes implements CancelarDocumentoService
{
    protected $context;
    protected $handler;

    /**
     * Constructor con inyección de dependencias.
     *
     * @param mixed $context
     * @param InfAjustesHandler $handler
     */
    public function __construct($context, InfAjustesHandler $handler)
    {
        $this->context = $context;
        $this->handler = $handler;
    }

    /**
     * Ejecuta el proceso de cancelación de ajustes.
     *
     * @return JsonResponse
     */
    public function run(): JsonResponse
    {
        DB::beginTransaction();

        try {
            Log::info('Inicio del proceso de cancelación de ajustes', ['context' => $this->context]);

            // Validar contexto
            $this->validateContext();

            // Actualizar desde WMS
            $result = $this->handler->actualizarDesdeWMS($this->context);

            if ($result['success']) {
                DB::commit();
                Log::info('Proceso completado correctamente.');
                return $this->successResponse("Proceso de cancelación de saldo sin problemas.");
            }

            DB::rollBack();
            Log::warning('Proceso con errores', ['message' => $result['message']]);
            return $this->errorResponse($result['message']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en el proceso de cancelación', ['error' => $e->getMessage()]);
            return $this->errorResponse("Error inesperado: " . $e->getMessage());
        }
    }

    /**
     * Valida el contexto de entrada.
     *
     * @throws Exception
     */
    private function validateContext(): void
    {
        if (empty($this->context)) {
            throw new Exception("El contexto proporcionado es inválido o vacío.");
        }
    }

    /**
     * Genera una respuesta de éxito.
     *
     * @param string $message
     * @return JsonResponse
     */
    private function successResponse(string $message): JsonResponse
    {
        return response()->json([
            "success" => true,
            "message" => $message
        ], 200);
    }

    /**
     * Genera una respuesta de error.
     *
     * @param string $message
     * @return JsonResponse
     */
    private function errorResponse(string $message): JsonResponse
    {
        return response()->json([
            "success" => false,
            "message" => $message
        ], 500);
    }
}
