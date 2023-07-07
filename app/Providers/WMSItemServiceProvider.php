<?php

namespace App\Providers;

use App\Models\cmproductos;
use App\WMS\Templates\Abstracts\ItemService;
use App\WMS\Templates\Implements\CreateItem;
use Illuminate\Support\ServiceProvider;

class WMSItemServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ItemService::class, function ($app) {
            if ($app->request->producto) {
                $model = cmproductos::sku($app->request->producto);
                return new CreateItem($model);
            }
            //return new CreateItem(null);
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
