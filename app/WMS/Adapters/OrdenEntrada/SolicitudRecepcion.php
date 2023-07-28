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
        return $model->wmscmdetgui->first()->gui_boddes;
    }

    protected function nroOrdenEntrada($model): string
    {
        return $model->gui_numero;
    }

    public function nroOrdenCliente($model): ?string
    {
        return $model->gui_ordcom;
    }
    
    public function codTipo($model): string
    {
        return 15;
    }

    public function codProveedor($model): string
    {
        return  $model->gui_subcta;
    }

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->gui_fechag, 'Y-m-d');
    }

    public function ordenEntradaDetalle($model): Collection
    {
        return  $model->wmscmdetgui->map(function ($model) {
            $detalle = new class($model) extends OrdenEntradaDetalleService
            {
                protected function codDeposito($model): string
                {
                    return $model->gui_boddes;
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
                /*
                public function item($model)
                {
                    if ($model?->cmproductos) {
                        return (new CreateItem($model->cmproductos))->get();
                    }
                }*/
            };

            return $detalle->get();
        });
    }
    public function proveedor($model)
    {
        return (new CreateProveedor($model->cmclientes))->get();
    }
}
