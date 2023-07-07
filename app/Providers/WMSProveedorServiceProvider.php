<?php

namespace App\Providers;

use App\WMS\Adapters\CreateProveedor;
use App\WMS\Contracts\WMSProveedorService;
use Illuminate\Support\ServiceProvider;

class WMSProveedorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WMSProveedorService::class, CreateProveedor::class);
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
