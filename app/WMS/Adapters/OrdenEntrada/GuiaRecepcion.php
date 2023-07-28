<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Adapters\CreateItem;
use App\WMS\Adapters\CreateProveedor;
use App\WMS\Contracts\OrdenEntradaDetalleService;
use App\WMS\Contracts\OrdenEntradaService;
use Illuminate\Support\Collection;

/**
 * Clase que representa la Guia de Recepcion, 
 */
class GuiaRecepcion extends OrdenEntradaService
{

    protected function codDeposito($model): string
    {
        return $model->cmdetgui->first()->gui_bodori;
    }

    protected function nroOrdenEntrada($model): string
    {
        return $model->gui_numero;
    }

    public function codTipo($model): string
    {
        return 8;
    }

    public function codProveedor($model): string
    {
        return  $model->gui_subcta;
    }

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->gui_fechag, 'Y-m-d');
    }

    public function codDepositoOrigen($model): ?string
    {
        return $model->cmdetgui->first()->gui_boddes;
    }

    public function nroOrdenCliente($model): ?string
    {
        return $model->gui_ordcom;
    }

    public function ordenEntradaDetalle($model): Collection
    {
        return  $model->cmdetgui->map(function ($model) {
            $detalle = new class($model) extends OrdenEntradaDetalleService
            {
                protected function codDeposito($model): string
                {
                    return $model->gui_bodori;
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
            };

            return $detalle->get();
        });
    }
    public function proveedor($model)
    {
        return (new CreateProveedor($model->cmclientes))->get();
    }
}
