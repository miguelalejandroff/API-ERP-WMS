<?php

namespace App\WMS\Providers;

use App\Models\cmguias;
use App\Models\guicompra;
use App\Models\wmscmguias;
use App\WMS\Adapters\OrdenEntrada\GuiaCompra;
use App\WMS\Adapters\OrdenEntrada\GuiaRecepcion;
use App\WMS\Adapters\OrdenEntrada\SolicitudRecepcion;
use App\WMS\Contracts\OrdenEntradaService;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class OrdenEntradaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenEntradaService::class, function ($app) {
            if ($app->request->guiaCompra) {
                $model = guicompra::Orden($app->request->guiaCompra);
                return new GuiaCompra($model);
            }
            if ($app->request->guiaRecepcion) {
                $model = cmguias::Orden($app->request->guiaRecepcion);
                return new GuiaRecepcion($model);
            }
            if ($app->request->solicitudRecepcion) {

                /**
                 * Buscar la solicitud de recepción basada en la solicitud entrante.
                 */
                $solicitudRecepcion = wmscmguias::solicitudesPromo($app->request->solicitudRecepcion);

                /**
                 * Devolver una nueva instancia de SolicitudRecepcion con la solicitud de recepción que incluye los detalles combinados.
                 */
                return new SolicitudRecepcion($solicitudRecepcion);
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
