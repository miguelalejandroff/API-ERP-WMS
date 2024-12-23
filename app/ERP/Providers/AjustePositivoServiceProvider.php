<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\Ajustes\AjustePositivo;
use App\ERP\Contracts\AjustePositivoService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class AjustePositivoServiceProvider extends ServiceProvider
{
    /**
     * Registra el servicio en el contenedor de la aplicación.
     */
    public function register()
    {
        $this->app->bind(AjustePositivoService::class, function ($app) {
            $context = $this->crearContexto($app->make(Request::class));

            Log::info('Contexto de Ajuste Positivo creado', [
                'trackingId' => $context->trackingId,
                'data' => $context->ajustePositivo,
            ]);

            return new AjustePositivo($context);
        });
    }

    /**
     * Crea el contexto necesario para el AjustePositivo.
     *
     * @param Request $request
     * @return object
     */
    private function crearContexto(Request $request)
    {
        // Filtrado de datos específicos requeridos para el ajuste positivo
        $datosAjuste = $request->only([
            'codigoProducto',
            'bodegaDestino',
            'cantidad',
            'descripcion',
            'fecha',
        ]);

        // Validar datos críticos (ejemplo: códigoProducto y cantidad son obligatorios)
        if (empty($datosAjuste['codigoProducto']) || empty($datosAjuste['cantidad'])) {
            throw new \InvalidArgumentException('Faltan datos requeridos: código del producto y cantidad.');
        }

        return (object)[
            'trackingId' => Str::uuid(), // ID único robusto
            'ajustePositivo' => (object)$datosAjuste,
        ];
    }
}
