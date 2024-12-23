<?php

namespace App\ERP\Providers;

use App\ERP\Adapters\OrdenSalida\SolicitudDespacho;
use App\ERP\Contracts\OrdenSalidaService;
use App\ERP\Enum\TipoDocumentoERP;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;

class OrdenSalidaServiceProvider extends ServiceProvider
{
    /**
     * Registra el servicio de OrdenSalida.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(OrdenSalidaService::class, function ($app) {
            // Crear contexto validado
            $context = $this->crearContexto($app->request->all());

            // Determinar la implementación basada en el tipo de documento
            return $this->resolverServicio($context);
        });
    }

    /**
     * Crea un contexto estructurado y validado.
     *
     * @param array $data
     * @return object
     * @throws CustomException
     */
    private function crearContexto(array $data)
    {
        // Validar campos requeridos
        if (empty($data['tipoDocumentoERP'])) {
            throw new CustomException("El campo 'tipoDocumentoERP' es obligatorio.", [], 400);
        }

        // Crear un ID de seguimiento
        $trackingId = uniqid('os_', true);

        $context = (object)[
            'trackingId' => $trackingId,
            'recepcion' => (object)$data,
        ];

        // Log del contexto creado
        Log::info('Contexto de OrdenSalida Creado', [
            'trackingId' => $trackingId,
            'tipoDocumentoERP' => $data['tipoDocumentoERP']
        ]);

        return $context;
    }

    /**
     * Resuelve la implementación según el tipo de documento.
     *
     * @param object $context
     * @return SolicitudDespacho
     * @throws CustomException
     */
    private function resolverServicio($context)
    {
        $tipoDocumento = $context->recepcion->tipoDocumentoERP;

        switch ($tipoDocumento) {
            case TipoDocumentoERP::SOLICITUD_DESPACHO->value:
                Log::info('Procesando SolicitudDespacho', ['trackingId' => $context->trackingId]);
                return new SolicitudDespacho($context);

            default:
                $errorMsg = "Tipo de Documento no válido: '{$tipoDocumento}'";
                Log::error($errorMsg, ['trackingId' => $context->trackingId]);
                throw new CustomException($errorMsg, [], 400);
        }
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
