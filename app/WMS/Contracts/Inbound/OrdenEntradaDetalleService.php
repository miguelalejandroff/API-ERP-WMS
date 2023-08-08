<?php

namespace App\WMS\Contracts\Inbound;

use Illuminate\Http\JsonResponse;
use stdClass;
use App\WMS\Build\AbstractBase;

abstract class OrdenEntradaDetalleService extends AbstractBase
{

    private static $lineaActual = 0;

    public function __construct($model)
    {
        parent::__construct($model);
        self::$lineaActual++;
    }

    public function get(): ?stdClass
    {
        $newData = parent::get();

        return $newData;
    }

    /**
     * Representa el CD, Site o Bodega a la cual se recibira la mercaderia
     */
    abstract protected function codDeposito($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function nroOrdenEntrada($model): string;

    /**
     * Numero unico de linea de detalle de orden
     */
    public function nroLinea($model): string
    {
        return self::$lineaActual;
    }

    /**
     * Codigo de Item (SKU)
     */
    public function codItem($model): ?string
    {
        return null;
    }

    /**
     * Codigo interno de moneda de la Orden
     */
    public function codMoneda($model): int
    {
        return 1;
    }
    /**
     * Cantidad original solicitada
     */
    public function cantidadSolicitada($model): ?int
    {
        return null;
    }

    public function getJson(): JsonResponse
    {
        $ordenEntradaDetalle = parent::get();
        return response()->json([
            'codOwner' => parent::codOwner(),
            'ordenEntradaDetalle' => [
                $ordenEntradaDetalle
            ]
        ]);
    }
}
