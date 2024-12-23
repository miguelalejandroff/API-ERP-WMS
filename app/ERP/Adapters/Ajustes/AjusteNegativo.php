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
            // Actualiza la base de datos utilizando el controlador
            $controller = $this->getController();
            $response = $controller->actualizarDesdeWMS($this->buildRequest());

            // Verifica el estado de la respuesta
            $this->validateResponse($response);

            // Procesa el saldo en bodega
            $this->processSaldoBodega();

            DB::commit();

            return response()->json(["message" => "Proceso de Ajuste Negativo completado sin problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError($e);
            return response()->json(["message" => "Error en el proceso de Ajuste Negativo: " . $e->getMessage()], 500);
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
            throw $e; // Lanza la excepción para manejarla en la transacción principal
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

        return new Request([
            'numeroDocumento' => $ajusteNegativoArray['numeroDocumento'] ?? null,
            'fechaRecepcionWMS' => $ajusteNegativoArray['fechaRecepcionWMS'] ?? null,
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
            $errorMessage = $response->getData()->message ?? 'Error desconocido';
            throw new Exception("Error en la respuesta del controlador: $errorMessage");
        }
    }

    /**
     * Registra el error en los logs.
     *
     * @param Exception $e
     */
    protected function logError(Exception $e): void
    {
        $errorMessage = sprintf(
            "Error en el proceso de Ajuste Negativo: %s. Datos de la solicitud: %s",
            $e->getMessage(),
            json_encode($this->context)
        );
        Log::error($errorMessage);
    }
}
