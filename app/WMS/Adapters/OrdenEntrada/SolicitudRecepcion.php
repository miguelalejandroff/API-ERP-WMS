<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Adapters\CreateItem;
use App\WMS\Adapters\CreateProveedor;
use App\WMS\Contracts\OrdenEntradaDetalleService;
use App\WMS\Contracts\OrdenEntradaService;
use Illuminate\Support\Collection;

class SolicitudRecepcion extends OrdenEntradaService
{

    protected function codDeposito($model): string
    {
        return "02";
    }

    protected function nroOrdenEntrada($model): string
    {
        return $model->gui_numero;
    }

    public function codTipo($model): string
    {
        return 1;
    }

    public function codProveedor($model): string
    {
        return  $model->gui_subcta;
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
                    return $model->gui_numero;
                }

                public function codItem($model): string
                {
                    return $model->gui_produc;
                }

                public function cantidadSolicitada($model): int
                {
                    return $model->gui_canord;
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
