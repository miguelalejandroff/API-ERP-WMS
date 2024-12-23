<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\TraspasoBodega\TraspasoBodega;
use App\ERP\Contracts\TraspasoBodegaService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class TraspasoBodegaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(TraspasoBodegaService::class, function ($app) {
            $context = (object)[
                'trackingId' => uniqid(),
                'traspasoBodega' => (object)$app->request->all()
            ];

            Log::info('Request Logged:', [
                'context' => $context,
            ]);

            // Ajusta la lógica para el tipo de documento o cualquier otro criterio necesario
            return new TraspasoBodega($context);
        });
    }
}
