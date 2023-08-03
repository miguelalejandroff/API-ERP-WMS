<?php

namespace App\WMS\Providers;

use App\Models\cmclientes;
use App\WMS\Adapters\Admin\CreateCliente;
use App\WMS\Contracts\Admin\ClienteService;
use Illuminate\Support\ServiceProvider;

class ClienteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClienteService::class, function ($app) {

            if ($app->request->has('cliente')) {
                $model = cmclientes::where('aux_claves', $app->request->cliente)->whereIn('aux_gruaux', [11, 12, 15, 50])->first();
                return new CreateCliente($model);
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
