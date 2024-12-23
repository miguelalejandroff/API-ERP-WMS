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

            // Log inicial para trazabilidad
            Log::info('Solicitud recibida en OrdenEntradaService', ['request_data' => $requestData]);

            // Crear contexto
            $context = new OrdenEntradaContext($requestData);
            $tipoDocumento = $context->recepcionWms->getDocumento('tipoDocumentoERP');
            $ordenEstado = $context->ordenCompra->getDocumento('ord_estado');

            // Determinar acción según el tipo de documento
            return $this->resolverServicio($context, $tipoDocumento, $ordenEstado);
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
            throw new CustomException("Orden de Compra Anulada: {$context->ordenCompra->getDocumento('ord_numcom')}", [], 500);
        }

        if ($ordenEstado === OrdenStatus::CERRADA->value) {
            throw new CustomException("Orden de Compra Cerrada: {$context->ordenCompra->getDocumento('ord_numcom')}", [], 500);
        }

        if (in_array($ordenEstado, [OrdenStatus::PENDIENTE->value, OrdenStatus::RECIBIDA->value])) {
            Log::info('Procesando Solicitud de Recepción', ['estado' => $ordenEstado]);
            $context->cargarDatosSolicitudRecepcion();
            return new SolicitudRecepcion($context);
        }

        throw new CustomException("Estado no válido: {$ordenEstado}", [], 500);
    }
}
