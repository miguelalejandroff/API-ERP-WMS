<?php

namespace App\WMS\Contracts\Outbound;

use Illuminate\Http\JsonResponse;
use stdClass;
use App\WMS\Build\AbstractBase;

abstract class OrdenSalidaDetalleService extends AbstractBase
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
    abstract protected function nroOrdenSalida($model): string;

    /**
     * Numero unico de linea de detalle de orden
     */
    public function nroLinea($model): string
    {
        return self::$lineaActual;
    }

    /**
     * Codigo interno de picking requerido por el sistema de origen
     */
    public function pickID($model): ?string
    {
        return 0;
    }

    /**
     * Codigo de Item (SKU)
     */
    public function codItem($model): ?string
    {
        return null;
    }

    /**
     * Codigo de Item (SKU)
     */
    public function cantidad($model): ?int
    {
        return null;
    }

    /**
     * Codigo interno de moneda de la Orden
     */
    public function montoNeto($model): int
    {
        return 1;
    }
    /**
     * Cantidad original solicitada
     */
    public function montoIVA($model): ?int
    {
        return null;
    }
    /**
     * Cantidad original solicitada
     */
    public function montoTotal($model): ?int
    {
        return null;
    }
    /**
     * Cantidad original solicitada
     */
    public function inventariable($model): ?string
    {
        return "S";
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
