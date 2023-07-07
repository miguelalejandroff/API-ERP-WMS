<?php

namespace App\WMS\Templates\Implements;

use App\Libs\WMS;
use App\WMS\Templates\Abstracts\OrdenEntradaService;
use App\WMS\Templates\Abstracts\OrdenEntradaDetalleService;
use Illuminate\Support\Collection;

class OrdenCompraRecepcion extends OrdenEntradaService
{

    protected function codDeposito($model): string
    {
        return "02";
    }

    protected function nroOrdenEntrada($model): string
    {
        return $model->ord_numcom;
    }

    public function codTipo($model): string
    {
        return 1;
    }

    public function codProveedor($model): string
    {
        return  $model->ord_subcta;
    }

    public function observacion($model): string
    {
        return $model->cmordobs?->ord_observ;
    }
    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->ord_fechac, 'Y-m-d');
    }

    public function ordenEntradaDetalle($model): Collection
    {
        return  $model->cmdetord->map(function ($model) {
            $detalle = new class($model) extends OrdenEntradaDetalleService
            {
                protected function codDeposito($model): string
                {
                    return "02";
                }

                protected function nroOrdenEntrada($model): string
                {
                    return $model->ord_numcom;
                }

                public function codItem($model): string
                {
                    return $model->ord_produc;
                }

                public function codMoneda($model): string
                {
                    return $model->cmordcom->ord_moneda;
                }

                public function cantidadSolicitada($model): int
                {
                    return $model->calculaCosto->saldoCalculado;
                }

                public function item($model)
                {
                    if ($model?->cmproductos) {
                        return (new CreateItem($model->cmproductos))->get();
                    }
                }
            };

            return $detalle->get();
        });
    }
    public function proveedor($model)
    {
        return (new CreateProveedor($model->cmclientes))->get();
    }
}
