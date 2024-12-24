<?php

namespace App\ERP\Adapters\Ajustes;

use App\ERP\Contracts\AjusteNegativoService;
use App\Http\Controllers\AjusteNegativoController;
use App\ERP\Handler\SaldoBodegaAjusNe;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AjusteNegativo implements AjusteNegativoService
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Ejecuta el proceso de ajuste negativo.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            $controller = $this->getController();
            $response = $controller->actualizarDesdeWMS($this->buildRequest());

            $this->validateResponse($response);
            $this->processSaldoBodega();

            DB::commit();

            return response()->json([
                "status" => "success",
                "message" => "Proceso completado con éxito."
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError($e);
            return response()->json([
                "status" => "error",
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene una instancia del controlador AjusteNegativoController.
     *
     * @return AjusteNegativoController
     */
    protected function getController(): AjusteNegativoController
    {
        return app(AjusteNegativoController::class);
    }

    /**
     * Procesa el saldo en bodega.
     */
    private function processSaldoBodega(): void
    {
        try {
            $handler = new SaldoBodegaAjusNe();
            $handler->handle($this->context);
            Log::info('SaldoBodegaAjusNe ejecutado con éxito.', ['context' => $this->context]);
        } catch (Exception $e) {
            Log::error('Error al ejecutar SaldoBodegaAjusNe: ' . $e->getMessage(), ['context' => $this->context]);
            throw new Exception("Error al procesar el saldo en bodega.", $e->getCode(), $e);
        }
    }

    /**
     * Construye la solicitud Request a partir del contexto.
     *
     * @return Request
     */
    protected function buildRequest(): Request
    {
        $ajusteNegativoArray = (array)$this->context->ajusteNegativo;

        // Validar si la fecha es válida
        $fechaRecepcionWMS = $ajusteNegativoArray['fechaRecepcionWMS'] ?? null;
        if ($fechaRecepcionWMS && !strtotime($fechaRecepcionWMS)) {
            throw new Exception("La fechaRecepcionWMS no es una fecha válida.");
        }

        return new Request([
            'numeroDocumento' => $ajusteNegativoArray['numeroDocumento'] ?? null,
            'fechaRecepcionWMS' => $fechaRecepcionWMS,
            'usuario' => $ajusteNegativoArray['usuario'] ?? null,
            'documentoDetalle' => $ajusteNegativoArray['documentoDetalle'] ?? [],
        ]);
    }

    /**
     * Valida la respuesta del controlador.
     *
     * @param $response
     * @throws Exception
     */
    protected function validateResponse($response): void
    {
        if ($response->status() !== 200) {
            $errorMessage = $response->getData()->message ?? 'Error desconocido en la respuesta del controlador';
            throw new Exception($errorMessage);
        }
    }

    /**
     * Registra el error en los logs.
     *
     * @param Exception $e
     */
    protected function logError(Exception $e): void
    {
        Log::error(sprintf(
            "Error: %s | Contexto: %s",
            $e->getMessage(),
            json_encode($this->context)
        ));
    }
}
