<?php

namespace App\Providers;

use App\WMS\Adapters\CreateOrdenDespacho;
use App\WMS\Contracts\WMSOrdenDespachoService;
use Illuminate\Support\ServiceProvider;

class WMSOrdenDespachoServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WMSOrdenDespachoService::class, CreateOrdenDespacho::class);
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
