<?php

namespace App\WMS\Contracts\Outbound;

use App\WMS\Build\AbstractBase;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

abstract class OrdenSalidaService extends AbstractBase
{
    /**
     * Representa la Bodega a la cual se recibira la mercaderia 
     */
    abstract protected function codDeposito($model): string;

    /**
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function nroOrdenSalida($model): string;

    /**
     * Numero opcional que puede referenciar al numero de orden de entrada
     */
    abstract protected function nroReferencia($model): string;

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
     * Corresponde al numero unico del documento de ingreso
     */
    abstract protected function tipoOrdenSalida($model): int;


    /**
     * Codigo de la moneda del documento
     */
    public function codMoneda($model): int
    {
        return 1;
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
     * Fecha en la que el sistema origen envia orden a WMS
     */
    public function fechaEmisionERP($model): ?string
    {
        return null;
    }

    /**
     * Fecha en la que el sistema origen envia orden a WMS
     */
    public function fechaCompromiso($model): ?string
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
     * Texto libre asociado a la orden
     */
    public function prioridad($model): ?string
    {
        return 3;
    }

    /**
     * Texto libre asociado a la orden
     */
    public function packingList($model): ?string
    {
        return "N";
    }


    /**
     * Texto libre asociado a la orden
     */
    public function crossDocking($model): ?string
    {
        return "N";
    }

    public function getJson(): JsonResponse
    {
        $ordenSalida = parent::get();
        Log::info('Orden de Salida JSON:', [
            'codOwner' => parent::codOwner(),
            'codDeposito' => $ordenSalida->codDeposito,
            'nroOrdenSalida' => $ordenSalida->nroOrdenSalida,
            'ordenSalida' => $ordenSalida,
        ]);
        return response()->json([
            'codOwner' => parent::codOwner(),
            'codDeposito' => $ordenSalida->codDeposito,
            'nroOrdenSalida' => $ordenSalida->nroOrdenSalida,
            'ordenSalida' => $ordenSalida,
        ]);
    }
}
