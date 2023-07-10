<?php

namespace App\Providers;

use App\WMS\Adapters\CreateCliente;
use App\WMS\Contracts\WMSClienteService;
use Illuminate\Support\ServiceProvider;

class WMSClienteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //$this->app->bind(WMSClienteService::class, CreateCliente::class);
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
