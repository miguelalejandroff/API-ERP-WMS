<?php

namespace App\ERP\Adapters\OrdenSalida;

use App\ERP\Contracts\OrdenSalidaService;
use App\Http\Controllers\DespachoController;
use App\Http\Controllers\DespachoClienteController;
use App\Http\Controllers\PedidosController;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SolicitudDespacho implements OrdenSalidaService
{
    protected $context;

    /**
     * Constructor.
     *
     * @param mixed $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * Ejecuta el proceso principal de despacho.
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            Log::info('SolicitudDespacho - Inicio del proceso de despacho', ['context' => $this->context]);

            // Validar contexto antes de procesar
            $this->validateContext();

            foreach ($this->context->recepcion->documentoDetalle as $detalle) {
                $controller = $this->getController($this->context->recepcion->tipoDocumentoERP);
                Log::info('SolicitudDespacho - Procesando detalle', ['detalle' => $detalle]);

                $controller->actualizardesdeWMS($this->buildRequest($detalle));
            }

            DB::commit();
            Log::info('SolicitudDespacho - Proceso completado exitosamente');

            return response()->json([
                "success" => true,
                "message" => "Proceso de Despacho sin Problemas"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('SolicitudDespacho - Error durante el proceso', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                "success" => false,
                "message" => "Error en el proceso de Despacho: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene el controlador adecuado basado en el tipo de documento ERP.
     *
     * @param string $tipoDocumentoERP
     * @return mixed
     */
    public function getController($tipoDocumentoERP)
    {
        $controllers = [
            'P'  => PedidosController::class,
            '16' => DespachoClienteController::class,
            'default' => DespachoController::class
        ];

        $controllerClass = $controllers[$tipoDocumentoERP] ?? $controllers['default'];

        Log::info('SolicitudDespacho - Selección de controlador', [
            'tipoDocumentoERP' => $tipoDocumentoERP,
            'controller' => $controllerClass
        ]);

        return app($controllerClass);
    }

    /**
     * Construye el request para actualizar desde WMS.
     *
     * @param array $detalle
     * @return Request
     */
    protected function buildRequest($detalle): Request
    {
        return new Request([
            'numeroDocumento' => $this->context->recepcion->numeroDocumento,
            'numeroOrdenSalida' => $this->context->recepcion->numeroOrdenSalida,
            'tipoDocumentoWMS' => $this->context->recepcion->tipoDocumentoWMS,
            'tipoDocumentoERP' => $this->context->recepcion->tipoDocumentoERP,
            'usuario' => $this->context->recepcion->usuario,
            'codigoProducto' => $detalle['codigoProducto'],
            'cantidadRecepcionada' => $detalle['cantidadRecepcionada'],
        ]);
    }

    /**
     * Valida el contexto para asegurar que tiene los datos requeridos.
     *
     * @throws Exception
     */
    private function validateContext()
    {
        if (empty($this->context->recepcion) || empty($this->context->recepcion->documentoDetalle)) {
            throw new Exception("El contexto de recepción es inválido o incompleto.");
        }
    }
}
