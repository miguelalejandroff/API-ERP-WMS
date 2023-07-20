<?php

namespace App\WMS\Providers;

use App\Models\cmclientes;
use App\WMS\Adapters\CreateProveedor;
use App\WMS\Contracts\ProveedorService;
use Illuminate\Support\ServiceProvider;

class ProveedorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ProveedorService::class, function ($app) {

            if ($app->request->has('proveedor')) {

                //14 = proveedor

                $model = cmclientes::where('aux_claves', $app->request->proveedor)->where('aux_gruaux', 14)->first();
                return new CreateProveedor($model);
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
