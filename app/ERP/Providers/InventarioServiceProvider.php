<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\Inventario\SolicitudInventario;
use App\ERP\Contracts\InventarioService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class InventarioServiceProvider extends ServiceProvider
{
    /**
     * Registra el servicio de Inventario.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(InventarioService::class, function ($app) {
            // Validar y estructurar los datos del contexto
            $context = $this->crearContexto($app);

            Log::info('Contexto de Inventario Creado', ['trackingId' => $context->trackingId]);

            // Devuelve la implementación concreta del servicio
            return new SolicitudInventario($context);
        });
    }

    /**
     * Crea y valida el contexto del servicio.
     *
     * @param mixed $app
     * @return object
     */
    private function crearContexto($app)
    {
        $request = $app->request;

        // Filtra únicamente los campos necesarios
        $datosFiltrados = $request->only([
            'tipoDocumento',
            'numeroDocumento',
            'detalles',
        ]);

        // Genera un ID de seguimiento único
        $trackingId = uniqid('inv_', true);

        // Retorna el contexto como un objeto
        return (object)[
            'trackingId' => $trackingId,
            'inventario' => (object)$datosFiltrados,
        ];
    }
}
