<?php

namespace App\WMS\Contracts\Inbound;

use Illuminate\Http\JsonResponse;
use App\WMS\Build\AbstractBase;

abstract class OrdenEntradaCambioEstadoService extends AbstractBase
{

    /**
     * Representa el CD, Site o Bodega a la cual se recibira la mercaderia
     */
    abstract protected function codDeposito($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function nroOrdenEntrada($model): string;

    /**
     * Codigo que identifica al nuevo estado para la orden (Valor 5 para cancelar orden)
     */
    protected function codEstado($model): string
    {
        return 5;
    }

    /**
     * Campo libre para indicar el motivo del cambio de estado de orden
     */
    abstract protected function observacion($model): string;


    public function getJson(): JsonResponse
    {
        $ordenEntradaCambioEstado = parent::get();
        return response()->json([
            'codOwner' => parent::codOwner(),
            'codDeposito' => $ordenEntradaCambioEstado->codDeposito,
            'ordenEntradaCambioEstado' => [
                $ordenEntradaCambioEstado
            ]
        ]);
    }
}
