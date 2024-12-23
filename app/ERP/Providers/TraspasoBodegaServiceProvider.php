<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\TraspasoBodega\TraspasoBodega;
use App\ERP\Contracts\TraspasoBodegaService;
use App\Exceptions\CustomException;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;

class TraspasoBodegaServiceProvider extends ServiceProvider
{
    /**
     * Registra el servicio TraspasoBodega.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(TraspasoBodegaService::class, function ($app) {
            // Crea y valida el contexto
            $context = $this->crearContexto($app->request->all());

            // Resuelve y retorna la implementación
            return $this->resolverServicio($context);
        });
    }

    /**
     * Crea un contexto validado con tracking ID único.
     *
     * @param array $data
     * @return object
     * @throws CustomException
     */
    private function crearContexto(array $data)
    {
        // Validar la estructura mínima del request
        if (empty($data['traspasoId']) || empty($data['bodegaOrigen']) || empty($data['bodegaDestino'])) {
            throw new CustomException(
                "Faltan campos obligatorios en la solicitud de TraspasoBodega: 'traspasoId', 'bodegaOrigen', 'bodegaDestino'.",
                [],
                400
            );
        }

        // Crear y retornar contexto
        $trackingId = uniqid('tb_', true);
        $context = (object)[
            'trackingId' => $trackingId,
            'traspasoBodega' => (object)$data,
        ];

        // Log del contexto
        Log::info('Contexto de TraspasoBodega Creado', [
            'trackingId' => $trackingId,
            'traspasoId' => $data['traspasoId'],
        ]);

        return $context;
    }

    /**
     * Resuelve el servicio de TraspasoBodega.
     *
     * @param object $context
     * @return TraspasoBodega
     */
    private function resolverServicio($context)
    {
        Log::info('Resolviendo servicio TraspasoBodega', [
            'trackingId' => $context->trackingId,
            'bodegaOrigen' => $context->traspasoBodega->bodegaOrigen,
            'bodegaDestino' => $context->traspasoBodega->bodegaDestino,
        ]);

        // Retorna la implementación de TraspasoBodega
        return new TraspasoBodega($context);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot() {}
}
