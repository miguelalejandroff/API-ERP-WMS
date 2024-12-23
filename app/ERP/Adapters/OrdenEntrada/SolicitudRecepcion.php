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

    public function __construct($context)
    {
        $this->context = $context;

        $this->initHandlers();
    }

    protected function initHandlers()
    {
        // Configurar la cadena de manejadores
        $guiaCompraHandler = new GuiaCompraHandler();
        $maestroProductoHandler = new MaestroProductoHandler();
        $guiaRecepcionHandler = new GuiaRecepcionHandler();
        $saldoBodegaHandler = new SaldoBodegaHandler();
        $saldoBodegaRestTransitoHandler = new SaldoBodegaRestTransitoHandler;

        $guiaCompraHandler->setNext($maestroProductoHandler);
        $maestroProductoHandler->setNext($guiaRecepcionHandler);
        $guiaRecepcionHandler->setNext($saldoBodegaHandler);
        $saldoBodegaHandler->setNext($saldoBodegaRestTransitoHandler);

        $this->firstHandler = $guiaCompraHandler;
    }
    public function run()
    {
        DB::beginTransaction();
        try {
            $this->firstHandler->execute($this->context);

            DB::commit();

            if ($this->context->guiaCompra->enviaWms) {
                $this->enviaOrdenEntradaWms(['guiaCompra' => $this->context->guiaCompra->getDocumento('gui_numero')]);
            }
            if ($this->context->guiaRecepcion->enviaWms) {
                $this->enviaOrdenEntradaWms(['guiaRecepcion' => $this->context->guiaRecepcion->getDocumento('gui_numero')]);
            }

            return response()->json(["message" => "Proceso de Recepcion sin Problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }

    public function enviaOrdenEntradaWms($document = [])
    {
        try {
            $url = url('/WMS/CreateOrdenEntrada');

            Http::post($url, $document);
        } catch (Exception $e) {
            Log::error('Error al obtener la ruta: ' . $e->getMessage());
        }
    }

}
