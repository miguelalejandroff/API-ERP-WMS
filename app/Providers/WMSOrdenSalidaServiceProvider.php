<?php

namespace App\Providers;

use App\Models\despachoencab;
use App\WMS\Adapters\OrdenSalida\SolicitudGuia;
use App\WMS\Contracts\WMSOrdenSalidaService;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class WMSOrdenSalidaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(WMSOrdenSalidaService::class, function ($app) {
            if ($app->request->solicitudGuia) {
                $model = despachoencab::SolicitudGuia($app->request->solicitudGuia);
                switch ($model->des_tipo) {
                    case '05':
                        return new SolicitudGuia($model);
                    case '06':
                        return new SolicitudGuia($model);
                    case '11':
                        return new SolicitudGuia($model);
                    case '48':
                        return new SolicitudGuia($model);
                    default:
                        throw new RuntimeException("Error Solicitud de Guia");
                }
            }
            if ($app->request->solicitudPedido) {
            }
            if ($app->request->factura) {
            }
            if ($app->request->boleta) {
            }
            if ($app->request->notaDebito) {
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
