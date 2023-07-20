<?php

namespace App\WMS\Providers;

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
                $model = wmscmguias::Orden($app->request->guiaRecepcion);
                return new GuiaRecepcion($model);
            }
            if ($app->request->solicitudRecepcion) {
                $model = wmscmguias::Orden($app->request->solicitudRecepcion);
                return new SolicitudRecepcion($model);
            }
            /*
            if ($app->request->orden) {
                $model = cmordcom::Orden($app->request->orden);
                switch ($model->ord_tipcom) {
                    case 'E':
                        return new OrdenCompraRecepcion($model);
                    case 'B':
                        $model = cmordcom::Orden($model->cmenlori?->bon_ordori);

                        if ($model) {
                            return new OrdenCompraRecepcion($model);
                        }

                    default:
                        throw new RuntimeException("Orden de Compra {$app->request->orden} No Existe ");
                }
            }
*/
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
