<?php

namespace App\WMS\Providers;

use App\Models\cmproductos;
use App\WMS\Adapters\Admin\CreateItem;
use App\WMS\Contracts\Admin\ItemService;
use Illuminate\Support\ServiceProvider;

class ItemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ItemService::class, function ($app) {
            
            if ($app->request->has('producto')) {
                $model = cmproductos::byProducto($app->request->producto);
                return new CreateItem($model);
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
