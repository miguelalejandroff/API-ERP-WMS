<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\OrdenSalida\SolicitudDespacho;
use App\ERP\Contracts\OrdenSalidaService;
use App\ERP\Enum\TipoDocumentoERP;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class OrdenSalidaServiceProvider extends ServiceProvider
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
        $this->app->bind(OrdenSalidaService::class, function ($app) {
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
            switch ($context->recepcion->tipoDocumentoERP) {
                case TipoDocumentoERP::SOLICITUD_DESPACHO->value:
                    //$context->solicitudDespacho = despachoencab::solicitudDespacho($context->recepcion->numeroDocumento);
                    return new SolicitudDespacho($context);
                default:
                    return new SolicitudDespacho($context);
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
