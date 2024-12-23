<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\CancelarDocumentoService;
use App\ERP\Handler\CancelarOrdSaldoHandler;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Exception;

class CancelarOrdSaldoAdapter implements CancelarDocumentoService
{
    protected $context;
    protected $handler;

    /**
     * Constructor con inyección de dependencias.
     *
     * @param mixed $context
     * @param CancelarOrdSaldoHandler $handler
     */
    public function __construct($context, CancelarOrdSaldoHandler $handler)
    {
        $this->context = $context;
        $this->handler = $handler;
    }

    /**
     * Ejecuta el proceso de cancelación de saldo.
     *
     * @return JsonResponse
     */
    public function run(): JsonResponse
    {
        DB::beginTransaction();

        try {
            Log::info("Iniciando proceso de cancelación de saldo", ['context' => $this->context]);

            // Procesa la actualización con el handler
            $result = $this->handler->actualizarDesdeWMS($this->context);

            // Valida el resultado del handler
            $this->validateResult($result);

            DB::commit();
            return $this->successResponse("Proceso de cancelación de saldo completado con éxito.");
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError($e);
            return $this->errorResponse("Error en la cancelación de saldo: " . $e->getMessage());
        }
    }

    /**
     * Valida el resultado del handler.
     *
     * @param array $result
     * @throws Exception
     */
    private function validateResult(array $result): void
    {
        if (!$result['success']) {
            throw new Exception($result['message']);
        }
    }

    /**
     * Genera una respuesta JSON exitosa.
     *
     * @param string $message
     * @return JsonResponse
     */
    private function successResponse(string $message): JsonResponse
    {
        return response()->json(["message" => $message], 200);
    }

    /**
     * Genera una respuesta JSON con error.
     *
     * @param string $message
     * @return JsonResponse
     */
    private function errorResponse(string $message): JsonResponse
    {
        return response()->json(["message" => $message], 500);
    }

    /**
     * Registra un error en los logs.
     *
     * @param Exception $e
     */
    private function logError(Exception $e): void
    {
        Log::error("Error durante el proceso de cancelación de saldo", [
            'error' => $e->getMessage(),
            'context' => $this->context,
        ]);
    }
}
