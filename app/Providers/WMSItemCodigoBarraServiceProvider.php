<?php

namespace App\Providers;

use App\WMS\Adapters\CreateItemCodigoBarra;
use App\WMS\Contracts\WMSItemCodigoBarraService;
use Illuminate\Support\ServiceProvider;

class WMSItemCodigoBarraServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(WMSItemCodigoBarraService::class, CreateItemCodigoBarra::class);
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
