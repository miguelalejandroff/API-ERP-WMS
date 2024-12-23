<?php

namespace App\ERP\Adapters\TraspasoBodega;

use App\ERP\Contracts\TraspasoBodegaService;
use App\Http\Controllers\TraspasoBodegaController;
use App\ERP\Handler\SaldoBodegaCanje;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Exception;

class TraspasoBodega implements TraspasoBodegaService
{
    protected $context;

    /**
     * Constructor que recibe el contexto.
     *
     * @param mixed $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Ejecuta el proceso principal de traspaso de bodega.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            Log::info('TraspasoBodega - Inicio del proceso', ['context' => $this->context]);

            $this->validateContext();

            // Obtener el controlador e iniciar el proceso
            $controller = $this->getController();
            $controller->actualizarDesdeWMS($this->buildRequest());

            // Ejecutar el manejo del saldo de bodega
            $this->saldoBodegaCanje();

            DB::commit();
            Log::info('TraspasoBodega - Proceso completado exitosamente.');

            return response()->json([
                "success" => true,
                "message" => "Proceso de Traspaso de Bodega completado sin problemas"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('TraspasoBodega - Error en el proceso', ['error' => $e->getMessage()]);

            return response()->json([
                "success" => false,
                "message" => "Error en Traspaso de Bodega: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el controlador de TraspasoBodega.
     *
     * @return \App\Http\Controllers\TraspasoBodegaController
     */
    protected function getController()
    {
        return app(TraspasoBodegaController::class);
    }

    /**
     * Maneja la lógica del saldo de bodega canje.
     */
    protected function saldoBodegaCanje()
    {
        try {
            $handler = new SaldoBodegaCanje();
            $handler->handle($this->context);

            Log::info('TraspasoBodega - SaldoBodegaCanje ejecutado con éxito.');
        } catch (Exception $e) {
            Log::error('TraspasoBodega - Error en SaldoBodegaCanje', ['error' => $e->getMessage()]);
            throw new Exception("Error al ejecutar SaldoBodegaCanje: " . $e->getMessage());
        }
    }

    /**
     * Construye el request para el controlador.
     *
     * @return \Illuminate\Http\Request
     */
    protected function buildRequest(): Request
    {
        $traspasoBodega = $this->context->traspasoBodega;

        return new Request([
            'numeroDocumento' => $traspasoBodega->numeroDocumento ?? null,
            'fechaRecepcionWMS' => $traspasoBodega->fechaRecepcionWMS ?? null,
            'usuario' => $traspasoBodega->usuario ?? null,
            'documentoDetalle' => $traspasoBodega->documentoDetalle ?? [],
        ]);
    }

    /**
     * Valida el contexto recibido.
     *
     * @throws Exception
     */
    protected function validateContext()
    {
        if (empty($this->context) || empty($this->context->traspasoBodega)) {
            throw new Exception("El contexto de Traspaso de Bodega es inválido o incompleto.");
        }
    }
}
