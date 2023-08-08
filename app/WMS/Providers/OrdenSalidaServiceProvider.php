<?php

namespace App\WMS\Providers;

use App\Exceptions\CustomException;
use App\Models\despachoencab;
use App\WMS\Adapters\OrdenSalida\SolicitudDespacho;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use Illuminate\Support\ServiceProvider;

class OrdenSalidaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenSalidaService::class, function ($app) {
            try {

                $tracking = $app->request->attributes->get('tracking');

                $adapter = null;

                switch (true) {
                    case $app->request->solicitudDespacho:
                        
                        $model = despachoencab::SolicitudGuia($app->request->solicitudDespacho);

                        $adapter = SolicitudDespacho::class;

                        break;
                    default:
                        throw new CustomException('No se proporciono ningun parametro valido', [], 500);
                }
                if (!$model) {
                    throw new CustomException('Modelo no encontrado', [], 500);
                }

                $trackingData['errors'] = null;
                $trackingData['status'] = 200;
                $trackingData['message'] = 'OK';

                $tracking->addTrackingData($trackingData);

                return new $adapter($model);
            } catch (CustomException $e) {
                $e->saveToDatabase();
                throw $e; // Cambia el 400 por el código de estado que corresponda
            }

            /*
            if ($app->request->guiaCompra) {
                $model = guicompra::Orden($app->request->guiaCompra);

                $trackingData['model'] = $model;
                $tracking->addTrackingData($trackingData);

                return new GuiaCompra($model);
            }
            if ($app->request->guiaRecepcion) {
                $model = cmguias::Orden($app->request->guiaRecepcion);

                $trackingData['model'] = $model;
                $tracking->addTrackingData($trackingData);

                return new GuiaRecepcion($model);
            }
            if ($app->request->solicitudRecepcion) {

                /**
                 * Buscar la solicitud de recepción basada en la solicitud entrante.
                 */
            /* $solicitudRecepcion = wmscmguias::solicitudesPromo($app->request->solicitudRecepcion);

                $trackingData['model'] = $solicitudRecepcion;
                $tracking->addTrackingData($trackingData);

                /**
                 * Devolver una nueva instancia de SolicitudRecepcion con la solicitud de recepción que incluye los detalles combinados.
                 */
            /*return new SolicitudRecepcion($solicitudRecepcion);
            }*/
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
