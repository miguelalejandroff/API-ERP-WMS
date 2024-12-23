<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\Inventario\SolicitudInventario;
use App\ERP\Contracts\InventarioService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class InventarioServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(InventarioService::class, function ($app) {
            $context = (object)[
                'trackingId' => uniqid(),
                'inventario' => (object)$app->request->all()
            ];

            Log::info('Request Logged:', [
                'context' => $context,
            ]);

            // Ajusta la lógica para el tipo de documento o cualquier otro criterio necesario
            return new SolicitudInventario($context);
        });
    }
}
