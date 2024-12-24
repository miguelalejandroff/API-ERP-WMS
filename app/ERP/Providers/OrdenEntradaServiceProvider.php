<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\OrdenEntrada\SolicitudRecepcion;
use App\ERP\Adapters\OrdenEntrada\DespachoTransito;
use App\ERP\Context\OrdenEntradaContext;
use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Enum\OrdenStatus;
use App\ERP\Enum\TipoDocumentoERP;
use App\Exceptions\CustomException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class OrdenEntradaServiceProvider extends ServiceProvider
{
    /**
     * Registra el servicio OrdenEntrada.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenEntradaService::class, function ($app) {
            $requestData = $app->request->all();

            Log::info('Solicitud recibida en OrdenEntradaService', ['request_data' => $requestData]);

            try {
                $context = new OrdenEntradaContext($requestData);

                if (!$context->ordenCompra) {
                    Log::error("ordenCompra no inicializado", ['request_data' => $requestData]);
                    throw new CustomException("Datos de Orden de Compra no válidos o incompletos", [], 400);
                }

                $tipoDocumento = $context->recepcionWms->getDocumento('tipoDocumentoERP');
                $ordenEstado = $context->ordenCompra->getDocumento('ord_estado');

                return $this->resolverServicio($context, $tipoDocumento, $ordenEstado);
            } catch (CustomException $e) {
                Log::error('Error en OrdenEntradaService', [
                    'error_message' => $e->getMessage(),
                    'request_data' => $requestData,
                ]);
                throw $e;
            } catch (\Exception $e) {
                Log::error('Error inesperado en OrdenEntradaService', [
                    'error_message' => $e->getMessage(),
                    'request_data' => $requestData,
                ]);
                throw new CustomException("Error interno en OrdenEntradaService", [], 500);
            }
        });
    }




    /**
     * Resuelve y devuelve el servicio correspondiente según el tipo de documento y estado.
     *
     * @param OrdenEntradaContext $context
     * @param string $tipoDocumento
     * @param string $ordenEstado
     * @return mixed
     * @throws CustomException
     */
    private function resolverServicio(OrdenEntradaContext $context, $tipoDocumento, $ordenEstado)
    {
        switch ($tipoDocumento) {
            case TipoDocumentoERP::SOLICITUD_RECEPCION->value:
                return $this->procesarSolicitudRecepcion($context, $ordenEstado);

            case TipoDocumentoERP::GUIA_DEVOLUCION->value:
            case TipoDocumentoERP::GUIA_DESPACHO->value:
            case TipoDocumentoERP::TRASPASO_SUCURSAL->value:
                Log::info('Procesando Despacho Transito', ['context' => $context]);
                return new DespachoTransito($context);

            default:
                $errorMsg = "Tipo de Documento no válido: '{$tipoDocumento}'";
                Log::error($errorMsg);
                throw new CustomException($errorMsg, [], 500);
        }
    }

    /**
     * Procesa la lógica específica para Solicitudes de Recepción.
     *
     * @param OrdenEntradaContext $context
     * @param string $ordenEstado
     * @return SolicitudRecepcion
     * @throws CustomException
     */
    private function procesarSolicitudRecepcion(OrdenEntradaContext $context, $ordenEstado)
    {
        // Validar estado de la Orden de Compra
        if ($ordenEstado === OrdenStatus::ANULADA->value) {
            $msg = "Orden de Compra Anulada: {$context->ordenCompra->getDocumento('ord_numcom')}";
            Log::error($msg);
            throw new CustomException($msg, [], 500);
        }

        if ($ordenEstado === OrdenStatus::CERRADA->value) {
            $msg = "Orden de Compra Cerrada: {$context->ordenCompra->getDocumento('ord_numcom')}";
            Log::error($msg);
            throw new CustomException($msg, [], 500);
        }

        if (in_array($ordenEstado, [OrdenStatus::PENDIENTE->value, OrdenStatus::RECIBIDA->value])) {
            Log::info('Procesando Solicitud de Recepción', ['estado' => $ordenEstado]);
            $context->cargarDatosSolicitudRecepcion();
            return new SolicitudRecepcion($context);
        }

        $msg = "Estado no válido: {$ordenEstado}";
        Log::error($msg);
        throw new CustomException($msg, [], 500);
    }
}
