<?php

namespace App\ERP\Adapters\Inventario;

use App\ERP\Contracts\InventarioService;
use App\Http\Controllers\InventarioController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolicitudInventario implements InventarioService
{
    protected $context;

    /**
     * Constructor de la clase.
     *
     * @param object $context Contexto que incluye los datos de inventario.
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Ejecuta el proceso de solicitud de inventario.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            // Obtiene el controlador de inventario y actualiza la base de datos
            $controller = $this->getController();
            $response = $controller->actualizarDesdeWMS($this->buildRequest());

            // Verifica si la respuesta fue exitosa
            $this->validateResponse($response);

            DB::commit();

            return response()->json(["message" => "Proceso de Inventario completado sin problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError($e);
            return response()->json(["message" => "Error en el proceso de Inventario: " . $e->getMessage()], 500);
        }
    }

    /**
     * Obtiene el controlador de inventario.
     *
     * @return InventarioController
     */
    protected function getController(): InventarioController
    {
        return app(InventarioController::class);
    }

    /**
     * Construye la solicitud Request a partir del contexto.
     *
     * @return Request
     */
    protected function buildRequest(): Request
    {
        $inventarioArray = (array)$this->context->inventario;

        return new Request([
            'numeroDocumento' => $inventarioArray['numeroDocumento'] ?? null,
            'fechaCierre' => $inventarioArray['fechaCierre'] ?? null,
            'Bodega' => $inventarioArray['Bodega'] ?? null,
            'usuario' => $inventarioArray['usuario'] ?? null,
            'documentoDetalle' => $inventarioArray['documentoDetalle'] ?? [],
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
     * Registra errores en los logs.
     *
     * @param Exception $e
     */
    protected function logError(Exception $e): void
    {
        $errorMessage = sprintf(
            "Error en el proceso de Inventario: %s. Datos de la solicitud: %s",
            $e->getMessage(),
            json_encode($this->context)
        );
        Log::error($errorMessage);
    }
}
