<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Adapters\Admin\CreateCliente;
use App\WMS\Adapters\Admin\CreateProveedor;
use App\WMS\Contracts\Inbound\OrdenEntradaDetalleService;
use App\WMS\Contracts\Inbound\OrdenEntradaService;
use Illuminate\Support\Collection;

class GuiaDespacho extends OrdenEntradaService
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
        switch ($model->gui_tipgui) { 
            case '05':
                return 5;
            case '06':
                return 6;
            case '11':
                return 12;
            case '48':
                return 48;
            case '39':
                return 39;
            case '21':
                return 21;
        }
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

    public function codSucursal($model): ?string
    {
        return $model->gui_sucori;
    }

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->gui_fechag, 'Y-m-d');
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
