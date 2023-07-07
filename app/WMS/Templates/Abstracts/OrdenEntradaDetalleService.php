<?php

namespace App\WMS\Templates\Abstracts;

use Illuminate\Http\JsonResponse;
use stdClass;

abstract class OrdenEntradaDetalleService extends AbstractBase
{

    private static $lineaActual = 0;

    protected $fields = [
        'codDeposito',
        'nroOrdenEntrada',
        'nroLinea',
        'codItem',
        'codMoneda',
        'cantidadSolicitada',
        'item'
    ];

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
     * 
     */
    public function nroLinea($model): string
    {
        return self::$lineaActual;
    }

    /**
     * 
     */
    public function codItem($model): ?string
    {
        return null;
    }

    /**
     * Codigo de la moneda del documento
     */
    public function codMoneda($model): string
    {
        return 1;
    }
    /**
     * Codigo de la moneda del documento
     */
    public function cantidadSolicitada($model): ?int
    {
        return null;
    }
    /**
     * Codigo de la moneda del documento
     */
    public function item($model)
    {
        return null;
    }

    public function getJson(): JsonResponse
    {
        return response()->json([
            'codOwner' => parent::codOwner()
        ]);
    }
}
