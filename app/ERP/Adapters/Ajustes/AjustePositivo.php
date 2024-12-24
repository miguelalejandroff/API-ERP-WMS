<?php

namespace App\ERP\Adapters\Ajustes;

use App\ERP\Contracts\AjustePositivoService;
use App\Http\Controllers\AjustePositivoController;
use App\ERP\Handler\SaldoBodegaAjusPos;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AjustePositivo implements AjustePositivoService
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Ejecuta el proceso de ajuste positivo.
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
     * Obtiene una instancia del controlador AjustePositivoController.
     *
     * @return AjustePositivoController
     */
    protected function getController(): AjustePositivoController
    {
        return app(AjustePositivoController::class);
    }

    /**
     * Procesa el saldo en bodega.
     */
    private function processSaldoBodega(): void
    {
        try {
            $handler = new SaldoBodegaAjusPos();
            $handler->handle($this->context);
            Log::info('SaldoBodegaAjusPos ejecutado con éxito.', ['context' => $this->context]);
        } catch (Exception $e) {
            Log::error('Error al ejecutar SaldoBodegaAjusPos: ' . $e->getMessage(), ['context' => $this->context]);
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
        $ajustePositivoArray = (array)$this->context->ajustePositivo;

        // Validar si la fecha es válida
        $fechaRecepcionWMS = $ajustePositivoArray['fechaRecepcionWMS'] ?? null;
        if ($fechaRecepcionWMS && !strtotime($fechaRecepcionWMS)) {
            throw new Exception("La fechaRecepcionWMS no es una fecha válida.");
        }

        return new Request([
            'numeroDocumento' => $ajustePositivoArray['numeroDocumento'] ?? null,
            'fechaRecepcionWMS' => $fechaRecepcionWMS,
            'usuario' => $ajustePositivoArray['usuario'] ?? null,
            'documentoDetalle' => $ajustePositivoArray['documentoDetalle'] ?? [],
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
