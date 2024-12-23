<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Adapters\Admin\CreateProveedor;
use App\WMS\Contracts\Inbound\OrdenEntradaDetalleService;
use App\WMS\Contracts\Inbound\OrdenEntradaService;
use Illuminate\Support\Collection;

/**
 * Clase que representa la Guia de Recepcion, 
 */
class GuiaRecepcion extends OrdenEntradaService
{

    protected function codDeposito($model): string
    {
        return $model->cmdetgui->first()->gui_boddes;
    }

    public function nroOrdenEntrada($model): string
    {
        return $model->gui_numero;
    }

    public function codTipo($model): string
    {
        return  8;
    }

    public function nroReferencia($model): string
    {
        return $model->gui_numero;
    }

    public function nroReferencia2($model): string
    {
        return $model->gui_tipgui;
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
        return $model->cmdetgui->first()->gui_bodori;
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
                    return $model->gui_boddes;
                }

                public function nroOrdenEntrada($model): string
                {
                    return $model->gui_numero;
                }

                public function codItem($model): string
                {
                    return $model->gui_produc;
                }

                public function cantidadSolicitada($model): float
                {
                    return $model->gui_canrep;
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
