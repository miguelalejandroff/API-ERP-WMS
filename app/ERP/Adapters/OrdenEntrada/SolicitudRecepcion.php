<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Handler\GuiaCompraHandler;
use App\ERP\Handler\GuiaRecepcionHandler;
use App\ERP\Handler\MaestroProductoHandler;
use App\ERP\Handler\SaldoBodegaHandler;
use App\ERP\Handler\SaldoBodegaRestTransitoHandler;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SolicitudRecepcion implements OrdenEntradaService
{
    protected $context;
    protected $firstHandler;

    /**
     * Constructor con inyección de contexto y configuración de manejadores.
     */
    public function __construct(
        $context,
        GuiaCompraHandler $guiaCompraHandler,
        MaestroProductoHandler $maestroProductoHandler,
        GuiaRecepcionHandler $guiaRecepcionHandler,
        SaldoBodegaHandler $saldoBodegaHandler,
        SaldoBodegaRestTransitoHandler $saldoBodegaRestTransitoHandler
    ) {
        $this->context = $context;

        // Configuración de la cadena de manejadores
        $guiaCompraHandler->setNext($maestroProductoHandler);
        $maestroProductoHandler->setNext($guiaRecepcionHandler);
        $guiaRecepcionHandler->setNext($saldoBodegaHandler);
        $saldoBodegaHandler->setNext($saldoBodegaRestTransitoHandler);

        $this->firstHandler = $guiaCompraHandler;
    }

    /**
     * Ejecuta el proceso principal.
     */
    public function run()
    {
        DB::beginTransaction();

        try {
            Log::info('SolicitudRecepcion - Inicio del proceso');

            // Ejecutar la cadena de manejadores
            $this->firstHandler->execute($this->context);

            DB::commit();

            Log::info('SolicitudRecepcion - Procesamiento completado, enviando a WMS si corresponde');

            // Enviar a WMS si es necesario
            if ($this->context->guiaCompra->enviaWms) {
                $this->enviaOrdenEntradaWms([
                    'guiaCompra' => $this->context->guiaCompra->getDocumento('gui_numero')
                ]);
            }

            if ($this->context->guiaRecepcion->enviaWms) {
                $this->enviaOrdenEntradaWms([
                    'guiaRecepcion' => $this->context->guiaRecepcion->getDocumento('gui_numero')
                ]);
            }

            Log::info('SolicitudRecepcion - Proceso finalizado correctamente');

            return response()->json([
                "success" => true,
                "message" => "Proceso de Recepción sin Problemas"
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('SolicitudRecepcion - Error durante el proceso', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                "success" => false,
                "message" => "Error en el proceso de Recepción: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Envía la orden de entrada a WMS.
     *
     * @param array $document
     */
    public function enviaOrdenEntradaWms(array $document)
    {
        $url = config('services.wms.create_orden_entrada'); // URL externa desde configuración

        try {
            Log::info('Enviando orden de entrada a WMS', ['url' => $url, 'document' => $document]);

            $response = Http::post($url, $document);

            if ($response->failed()) {
                throw new Exception("Error al enviar orden a WMS: " . $response->body());
            }

            Log::info('Orden enviada a WMS con éxito', ['response' => $response->json()]);
        } catch (Exception $e) {
            Log::error('Error al enviar orden a WMS', ['error' => $e->getMessage()]);
            throw $e; // Relanza la excepción para que DB::rollback capture el error
        }
    }
}
