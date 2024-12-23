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
            // Usa el controlador para actualizar la base de datos
            $controller = $this->getController();
            $response = $controller->actualizarDesdeWMS($this->buildRequest());

            // Verifica el estado de la respuesta
            $this->validateResponse($response);

            // Procesa el saldo en bodega
            $this->processSaldoBodega();

            DB::commit();

            return response()->json(["message" => "Proceso de Ajuste Positivo completado sin problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError($e);
            return response()->json(["message" => "Error en el proceso de Ajuste Positivo: " . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene el controlador para manejar ajustes positivos.
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
        $ajustePositivoArray = (array)$this->context->ajustePositivo;

        return new Request([
            'numeroDocumento' => $ajustePositivoArray['numeroDocumento'] ?? null,
            'fechaRecepcionWMS' => $ajustePositivoArray['fechaRecepcionWMS'] ?? null,
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
            $errorMessage = $response->getData()->message ?? 'Error desconocido';
            throw new Exception("Error en la respuesta del controlador: $errorMessage");
        }
    }

    /**
     * Registra los errores en los logs.
     *
     * @param Exception $e
     */
    protected function logError(Exception $e): void
    {
        $errorMessage = sprintf(
            "Error en el proceso de Ajuste Positivo: %s. Datos de la solicitud: %s",
            $e->getMessage(),
            json_encode($this->context)
        );
        Log::error($errorMessage);
    }
}
