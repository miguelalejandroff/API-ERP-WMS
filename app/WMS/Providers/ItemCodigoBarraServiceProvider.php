<?php

namespace App\WMS\Providers;

use App\Models\cmproductos;
use App\WMS\Adapters\Admin\CreateItemCodigoBarra;
use App\WMS\Contracts\Admin\ItemCodigoBarraService;
use Illuminate\Support\ServiceProvider;
use App\WMS\Adapters\Admin\CreateItem;
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
                $model = cmproductos::byProducto($app->request->codigoBarra);
                return new CreateItem($model);

                return  $model->wmscodigobarra->map(function ($model) {
                    if (!empty($model->codigo_barra) && !empty($model->tipo_codigo)) {
                        return (new CreateItemCodigoBarra($model))->get();
                    }
                });
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
