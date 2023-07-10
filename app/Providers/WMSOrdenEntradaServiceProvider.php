<?php

namespace App\Providers;

use App\Models\cmfactura;
use App\Models\cmguias;
use App\Models\cmordcom;
use App\WMS\Adapters\OrdenEntrada\Guia;
use App\WMS\Adapters\OrdenEntrada\NotaCredito;
use App\WMS\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\WMS\Contracts\WMSOrdenEntradaService;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class WMSOrdenEntradaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
     /*   $this->app->bind(WMSOrdenEntradaService::class, function ($app) {


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

            if ($app->request->notaCredito) {
                $model = cmfactura::NotaCredito($app->request->notaCredito);
                switch ($model->fac_tipdoc) {
                    case '13':
                        return new NotaCredito($model);
                    case '19':
                        return new NotaCredito($model);
                    case '37':
                        return new NotaCredito($model);
                    case '38':
                        return new NotaCredito($model);
                    default:
                        throw new RuntimeException("Nota Credito {$app->request->notaCredito} No Existe ");
                }
            }

            if ($app->request->guia) {
                $model = cmguias::Guia($app->request->guia);
                switch ($model->fac_tipdoc) {
                    case '05':
                        return new Guia($model);
                    case '06':
                        return new Guia($model);
                    case '11':
                        return new Guia($model);
                    default:
                        throw new RuntimeException("Guia {$app->request->guia} No Existe ");
                }
            }
        });*/
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
