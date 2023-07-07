<?php

namespace App\Providers;

use App\ERP\Adapters\OrdenEntrada\Guia;
use App\ERP\Adapters\OrdenEntrada\NotaCredito;
use App\ERP\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use Illuminate\Support\ServiceProvider;
use RuntimeException;
use App\Http\Controllers\Logs\Log;

class ERPOrdenEntradaServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ERPOrdenEntradaService::class, function ($app) {
            Log::append('CreateOrdenEntrdaERP', "{$app->request}");
            switch ($app->request->codTipo) {
                case '01':
                    return new OrdenCompraRecepcion($app->request);
                case '02':
                    return new OrdenCompraRecepcion($app->request);
                case '05':
                    return new Guia($app->request);
                case '06':
                    return new Guia($app->request);
                case '11':
                    return new Guia($app->request);
                case '13':
                    return new NotaCredito($app->request);
                case '19':
                    return new NotaCredito($app->request);
                case '37':
                    return new NotaCredito($app->request);
                case '38':
                    return new NotaCredito($app->request);
                default:
                    throw new RuntimeException("Error");
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
