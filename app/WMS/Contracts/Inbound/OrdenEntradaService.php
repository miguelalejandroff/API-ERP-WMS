<?php

namespace App\WMS\Contracts\Inbound;

use App\Libs\WMS;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use App\WMS\Build\AbstractBase;

abstract class OrdenEntradaService extends AbstractBase
{
    /**
     * Representa la Bodega a la cual se recibira la mercaderia 
     */
    abstract protected function codDeposito($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function nroOrdenEntrada($model): string;

    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    public function nroReferencia($model): ?string
    {
        return null;
    }

    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    abstract protected function nroReferencia2($model): string;
  

    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    public function nroOrdenCliente($model): ?string
    {
        return null;
    }

    /**
     * Codigo del tipo de orden (definida segun la parametrizacion por cada proyecto)
     */
    abstract protected function codTipo($model): ?string;

    /**
     * Codigo del Proveedor
     */
    public function codProveedor($model): ?string
    {
        return null;
    }

    /**
     * Codigo de cliente (En caso de devolucion)
     */
    public function codCliente($model): ?string
    {
        return null;
    }

    /**
     * Codigode Sucursal (de cliente, en caso de devolucion)
     */
    public function codSucursal($model): ?string
    {
        return null;
    }

    /**
     * Valor que indica el valor en moneda extranjera de la recepcion
     */
    public function tipoDeCambio($model): int
    {
        return 0;
    }

    /**
     * Fecha planificada del arribo de la mercaderia al CD
     */
    public function fechaEstimadaRecepcion($model): ?string
    {
        return  WMS::now();
    }

    /**
     * Fecha planificada de duracion de la orden
     */
    public function fechaExpiracion($model): ?string
    {
        return  WMS::nowYear();
    }

    /**
     * Fecha en la que el sistema origen envia orden a WMS
     */
    public function fechaEmisionERP($model): ?string
    {
        return null;
    }

    /**
     * 
     */
    public function codDepositoOrigen($model): ?string
    {
        return null;
    }

    /**
     * 
     */
    public function codDepositoOrigen2($model): ?string
    {
        return null;
    }

    /**
     * Texto libre asociado a la orden
     */
    public function observacion($model): ?string
    {
        return null;
    }

    /**
     * Clasificacion de la orden que puede indicar si es NACIONAL o IMPORTADA
     */
    public function origen($model): ?string
    {
        return null;
    }

    /**
     * 
     */
    public function requiereVas($model): ?string
    {
        return null;
    }

    /**
     * 
     */
    public function esCrossDocking($model): ?string
    {
        return null;
    }

    /**
     * 
     */
    public function nroCrossDocking($model): ?string
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
     * 
     */
    abstract protected function ordenEntradaDetalle($model): Collection;

    /**
     * 
     */
    public function proveedor($model)
    {
        return null;
    }

    /**
     * 
     */
    public function cliente($model)
    {
        return null;
    }

    public function getJson(): JsonResponse
    {

        $ordenEntrada = parent::get();
        return response()->json([
            'codOwner' => parent::codOwner(),
            'codDeposito' => $ordenEntrada->codDeposito,
            'nroOrdenEntrada' => $ordenEntrada->nroOrdenEntrada,
            'ordenEntrada' => $ordenEntrada,
        ]);
    }
}
