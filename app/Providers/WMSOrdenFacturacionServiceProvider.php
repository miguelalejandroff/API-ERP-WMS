<?php

namespace App\Providers;

use App\WMS\Adapters\CreateOrdenFacturacion;
use App\WMS\Contracts\WMSOrdenFacturacionService;
use Illuminate\Support\ServiceProvider;

class WMSOrdenFacturacionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WMSOrdenFacturacionService::class, CreateOrdenFacturacion::class);
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
