<?php

namespace App\ERP;

use App\ERP\Adapters\OrdenEntrada\Guia;
use App\ERP\Adapters\OrdenEntrada\NotaCredito;
use App\ERP\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\ERP\Adapters\OrdenEntrada\SolicitudRecepcion;
use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Contracts\OrdenSalidaService;
use Illuminate\Http\Request;
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
    public function confirmarOrdenSalida(OrdenSalidaService $ordenSalida)
    {
        return $ordenSalida->run();
    }
}
