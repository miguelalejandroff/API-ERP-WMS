<?php

namespace App\Providers;

use App\WMS\Adapters\CreateOrdenSalidaOMS;
use App\WMS\Contracts\WMSOrdenSalidaOMSService;
use Illuminate\Support\ServiceProvider;

class WMSOrdenSalidaOMSServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->bind(WMSOrdenSalidaOMSService::class, CreateOrdenSalidaOMS::class);
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
