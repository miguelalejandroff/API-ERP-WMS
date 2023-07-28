<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\OrdenEntrada\SolicitudRecepcion;
use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Enum\OrdenStatus;
use App\ERP\Enum\TipoDocumentoERP;
use App\Models\cmclientes;
use App\Models\cmordcom;
use App\Models\wmscmguias;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OrdenEntradaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * Vincula la interfaz OrdenEntradaService a una función anónima que resuelve la implementación.
         */
        $this->app->bind(OrdenEntradaService::class, function ($app) {
            /**
             * Crea un objeto de contexto para mantener los datos relevantes.
             */
            $context = (object)[
                'trackingId' => uniqid(),
                'recepcion' => (object)$app->request->all()
            ];


            Log::info('Request Logged:', [
                'context' => $context,
            ]);
            
            $context->recepcion->fechaRecepcionWMS = Carbon::parse($context->recepcion->fechaRecepcionWMS);

            /**
             * Maneja diferentes tipos de documentos.
             */
            switch ($context->recepcion->tipoDocumentoERP) {
                case TipoDocumentoERP::SOLICITUD_RECEPCION->value:

                    /**
                     * Busca la solicitud de recepción basándose en el número de documento.
                     */
                    $context->solicitudRecepcion = wmscmguias::solicitudesPromo($context->recepcion->numeroDocumento);
                    /**
                     * Lanza una excepción si la solicitud de recepción no existe.
                     */
                    if (!$context->solicitudRecepcion) {
                        throw new Exception("Solicitud de Recepcion no Existe: {$context->recepcion->numeroDocumento}", 500);
                    }

                    /**
                     * Busca la orden de compra.
                     */
                    $context->ordenCompra = cmordcom::Orden($context->solicitudRecepcion->gui_ordcom);

                    /**
                     * Lanza una excepción si la orden de compra no existe.
                     */
                    if (!$context->ordenCompra) {
                        throw new Exception("Orden de Compra no Existe: {$context->solicitudRecepcion->gui_ordcom}", 500);
                    }

                    /**
                     * Maneja diferentes estados de la orden de compra.
                     */
                    switch ($context->ordenCompra->ord_estado) {
                        case OrdenStatus::RECIBIDA->value:
                            throw new Exception("Orden de Compra Recepcionada: {$context->ordenCompra->ord_numcom}", 500);
                        case OrdenStatus::ANULADA->value:
                            throw new Exception("Orden de Compra Anulada: {$context->ordenCompra->ord_numcom}", 500);
                        case OrdenStatus::CERRADA->value:
                            throw new Exception("Orden de Compra Cerrada: {$context->ordenCompra->ord_numcom}", 500);
                        case OrdenStatus::PENDIENTE->value:

                            /**
                             * Si existe una orden de compra bonificada, la busca.
                             */
                            if ($context->ordenCompra->cmenlbon?->bon_ordbon) {
                                $context->ordenCompraBonificada = cmordcom::Orden($context->ordenCompra->cmenlbon->bon_ordbon);
                            }

                            /**
                             * Busca el proveedor.
                             */
                            $context->proveedor = cmclientes::Cliente($context->solicitudRecepcion->gui_subcta);

                            /**
                             * Ejecuta la solicitud de recepción.
                             */
                            return new SolicitudRecepcion($context);
                    }
            }
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
