<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\Ajustes\AjustePositivo;
use App\ERP\Contracts\AjustePositivoService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class AjustePositivoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AjustePositivoService::class, function ($app) {
            $context = (object)[
                'trackingId' => uniqid(),
                'ajustePositivo' => (object)$app->request->all()
            ];

            Log::info('Request Logged:', [
                'context' => $context,
            ]);

            // Ajusta la lógica para el tipo de documento o cualquier otro criterio necesario
            return new AjustePositivo($context);
        });
    }
}
