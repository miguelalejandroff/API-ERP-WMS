<?php

namespace App\ERP;

use App\ERP\Contracts\{
    OrdenEntradaService,
    InventarioService,
    AjustePositivoService,
    AjusteNegativoService,
    TraspasoBodegaService,
    CancelarDocumentoService,
    OrdenSalidaService
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EndpointERP
{
    public function __construct(public Request $request) {}

    /**
     * Generaliza la ejecución de servicios ERP con manejo de excepciones.
     */
    private function executeService($service, string $operation, array $messages = [])
    {
        try {
            Log::info("Inicio de {$operation}");

            // Ejecutar el servicio
            $response = $service->run();

            // Respuesta específica basada en clase si se proporciona
            if (!empty($messages)) {
                foreach ($messages as $class => $message) {
                    if ($service instanceof ("App\\ERP\\Adapters\\OrdenEntrada\\{$class}")) {
                        return response()->json(["message" => $message]);
                    }
                }
                return response()->json(["error" => "Tipo de operación no reconocido"], 400);
            }

            Log::info("Operación {$operation} completada");
            return $response;
        } catch (\Exception $e) {
            return $this->handleException($e, $operation);
        }
    }

    public function confirmarOrdenEntrada(OrdenEntradaService $ordenEntrada)
    {
        return $this->executeService($ordenEntrada, 'confirmarOrdenEntrada');
    }

    public function confirmarOrdenEntrada2(OrdenEntradaService $ordenEntrada)
    {
        $messages = [
            'OrdenCompraRecepcion' => "Orden de compra Recepcionada",
            'NotaCredito' => "Nota de crédito Recepcionada",
            'Guia' => "Guía Recepcionada"
        ];

        return $this->executeService($ordenEntrada, 'confirmarOrdenEntrada2', $messages);
    }

    public function confirmarInventario(InventarioService $inventario)
    {
        return $this->executeService($inventario, 'confirmarInventario');
    }

    public function confirmarAjustePositivo(AjustePositivoService $ajustePositivo)
    {
        return $this->executeService($ajustePositivo, 'confirmarAjustePositivo');
    }

    public function confirmarAjusteNegativo(AjusteNegativoService $ajusteNegativo)
    {
        return $this->executeService($ajusteNegativo, 'confirmarAjusteNegativo');
    }

    public function confirmarTraspasoBodega(TraspasoBodegaService $traspasoBodega)
    {
        return $this->executeService($traspasoBodega, 'confirmarTraspasoBodega');
    }

    public function confirmarOrdenSalida(OrdenSalidaService $ordenSalida)
    {
        return $this->executeService($ordenSalida, 'confirmarOrdenSalida');
    }

    public function confirmarCancelarDocumento(CancelarDocumentoService $cancelarDocumento)
    {
        return $this->executeService($cancelarDocumento, 'confirmarCancelarDocumento');
    }

    /**
     * Manejo centralizado de excepciones.
     *
     * @param \Exception $e
     * @param string $operation
     * @return \Illuminate\Http\JsonResponse
     */
    private function handleException(\Exception $e, $operation)
    {
        Log::error("Error en {$operation}: " . $e->getMessage(), [
            'stack_trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'error' => "Ocurrió un error en {$operation}",
            'details' => $e->getMessage()
        ], 500);
    }
}
