<?php

namespace App\ERP;

use App\ERP\Adapters\OrdenEntrada\Guia;
use App\ERP\Adapters\OrdenEntrada\NotaCredito;
use App\ERP\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\ERP\Adapters\OrdenEntrada\SolicitudRecepcion;
use App\ERP\Adapters\OrdenEntrada\DespachoTransito;
use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Contracts\InventarioService;
use App\ERP\Contracts\AjustePositivoService;
use App\ERP\Contracts\AjusteNegativoService;
use App\ERP\Contracts\TraspasoBodegaService;
use App\ERP\Contracts\CancelarDocumentoService;
use App\ERP\Contracts\OrdenSalidaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class EndpointERP
{
    public function __construct(public Request $request)
    {
    }
    public function confirmarOrdenEntrada(OrdenEntradaService $ordenEntrada)
    {

        if ($ordenEntrada instanceof SolicitudRecepcion) {
            return $ordenEntrada->run();
        }

        if ($ordenEntrada instanceof DespachoTransito) {
            return $ordenEntrada->run();
        }
    }

    public function confirmarOrdenEntrada2(Request $orden)
    {
        dd($orden);
        if ($orden instanceof OrdenCompraRecepcion) {
            return response()->json(["message" => "Orden de compra Recepcionada"]);
        }
        if ($orden instanceof NotaCredito) {
            return response()->json(["message" => "Nota de credito Recepcionada"]);
        }
        if ($orden instanceof Guia) {
            return response()->json(["message" => "Guia Recepcionada"]);
        }
    }
    public function confirmarInventario(InventarioService $inventario)
    {
        try {
            Log::info('Inicio de confirmarInventario');
    
            // Ejecutar el servicio de inventario
            $response = $inventario->run();
    
            Log::info('Operación confirmarInventario completada');
    
            return $response;
        } catch (\Exception $e) {
            Log::error('Error en confirmarInventario: ' . $e->getMessage());
    
            // Puedes manejar el error según tus necesidades
            return response()->json(['error' => 'Ocurrió un error en confirmarInventario'], 500);
        }
    }
    

    public function confirmarAjustePositivo(AjustePositivoService $ajustePositivo)
    {
        return $ajustePositivo->run();
    }

    public function confirmarAjusteNegativo(AjusteNegativoService $ajusteNegativo)
    {
        return $ajusteNegativo->run();
    }

    public function confirmarTraspasoBodega(TraspasoBodegaService $traspasoBodega)
    {
        return $traspasoBodega->run();
    }

    public function confirmarOrdenSalida(OrdenSalidaService $ordenSalida)
    {
        return $ordenSalida->run();
    }

    public function confirmarCancelarDocumento (CancelarDocumentoService $cancelarDocumento)
    {
        return $cancelarDocumento->run();
    }

}
