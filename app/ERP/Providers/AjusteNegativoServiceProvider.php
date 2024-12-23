<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\Ajustes\AjusteNegativo;
use App\ERP\Contracts\AjusteNegativoService;
use Illuminate\Support\ServiceProvider;
use App\ERP\Context\OrdenEntradaContext;
use App\ERP\Enum\TipoDocumentoERP;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;

class AjusteNegativoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(AjusteNegativoService::class, function ($app) {
            $context = (object)[
                'trackingId' => uniqid(),
                'ajusteNegativo' => (object)$app->request->all()
            ];

            Log::info('Request Logged:', [
                'context' => $context,
            ]);
                    return new AjusteNegativo($context);
            });
            

    }

}
