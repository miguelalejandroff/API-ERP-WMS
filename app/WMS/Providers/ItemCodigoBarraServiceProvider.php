<?php

namespace App\WMS\Providers;

use App\Models\wmscodigobarra;
use App\WMS\Adapters\CreateItemCodigoBarra;
use App\WMS\Contracts\ItemCodigoBarraService;
use Illuminate\Support\ServiceProvider;

class ItemCodigoBarraServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ItemCodigoBarraService::class, function ($app) {

            if ($app->request->has('codigoBarra')) {
                $model = wmscodigobarra::where('codigo_antig', $app->request->codigoBarra)->first();
                return new CreateItemCodigoBarra($model);
            }

            throw new \Exception('El modelo no fue definido');
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
