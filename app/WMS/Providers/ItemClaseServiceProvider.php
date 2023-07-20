<?php

namespace App\WMS\Providers;

use App\Models\grupos;
use App\Models\rubros;
use App\WMS\Adapters\CreateRubro;
use App\WMS\Adapters\CreateSubRubro;
use App\WMS\Contracts\ItemClaseService;
use Illuminate\Support\ServiceProvider;

class ItemClaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ItemClaseService::class, function ($app) {

            if ($app->request->has('rubro')) {
                $model = rubros::where('cod_rubro', $app->request->rubro)->first();
                return new CreateRubro($model);
            }
            if ($app->request->has('subrubro')) {
                $model = grupos::where('cod_rg', $app->request->subrubro)->first();
                return new CreateSubRubro($model);
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
