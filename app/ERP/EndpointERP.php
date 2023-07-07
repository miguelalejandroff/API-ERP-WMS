<?php

namespace App\ERP;

use App\ERP\Adapters\OrdenEntrada\Guia;
use App\ERP\Adapters\OrdenEntrada\NotaCredito;
use App\ERP\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\ERP\Contracts\ERPOrdenEntradaService;
use Illuminate\Http\Request;
use RuntimeException;

class EndpointERP
{
    public function __construct(public Request $request)
    {
        //ddd
    }
    public function createOrdenEntrada(ERPOrdenEntradaService $orden)
    {

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
    public function createOrdenSalida(Request $request)
    {
    }
}
