<?php

namespace App\WMS\Providers;

use App\Models\wmscodigobarra;
use App\WMS\Adapters\Admin\CreateItemCodigoBarra;
use App\WMS\Contracts\Admin\ItemCodigoBarraService;
use App\Models\cmproductos;
use App\Models\enlacewms;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

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
                $model = wmscodigobarra::where('codigo_antig', $app->request->codigoBarra)->get();
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
