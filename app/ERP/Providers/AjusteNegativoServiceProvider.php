<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\Ajustes\AjusteNegativo;
use App\ERP\Contracts\AjusteNegativoService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AjusteNegativoServiceProvider extends ServiceProvider
{
    /**
     * Registra el servicio en el contenedor de la aplicación.
     */
    public function register()
    {
        $this->app->bind(AjusteNegativoService::class, function ($app) {
            $context = $this->crearContexto($app->make(Request::class));

            Log::info('Contexto de Ajuste Negativo creado', [
                'trackingId' => $context->trackingId,
                'data' => $context->ajusteNegativo,
            ]);

            return new AjusteNegativo($context);
        });
    }

    /**
     * Crea el contexto requerido para el servicio AjusteNegativo.
     *
     * @param Request $request
     * @return object
     */
    private function crearContexto(Request $request)
    {
        // Validación y filtrado seguro de datos
        $datosAjuste = $request->only([
            'codigoProducto',
            'bodegaOrigen',
            'cantidad',
            'descripcion',
            'fecha',
        ]);

        // Validar que no falten datos críticos
        if (empty($datosAjuste['codigoProducto']) || empty($datosAjuste['cantidad'])) {
            throw new \InvalidArgumentException('Faltan datos requeridos: código del producto y cantidad.');
        }

        return (object)[
            'trackingId' => Str::uuid(), // Genera un UUID más robusto que uniqid()
            'ajusteNegativo' => (object)$datosAjuste,
        ];
    }
}
