<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\Libs\WMS;
use App\WMS\Adapters\Admin\CreateCliente;
use App\Models\cmclientes;
use App\WMS\Adapters\Admin\CreateProveedor;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use App\WMS\Contracts\Outbound\OrdenSalidaDetalleService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


/**
 * Clase que representa la Guia de Recepcion, 
 */
class Ajustesalida extends OrdenSalidaService
{

    protected function codDeposito($model): string
    {
        return $model->cmdetgui->first()->gui_bodori;
    }

    public function nroOrdenSalida($model): string
    {
        return $model->gui_numero;
    }

    public function nroOrdenCliente($model): string
    {
        if($model->gui_tipgui == '10') {
            return $model->gui_numero;
        }else {
            return $model->gui_ordcom;
        }
    }

    protected function tipoOrdenSalida($model): int
    {
        switch ($model->gui_tipgui) { 
            case '10':
                return 10;
            case '40':
                return 40;
            case '41':
                return 41;
            case '47':
                return 47;
            }

    }

    public function nroReferencia($model): string
    {
        if($model->gui_tipgui == '10') {
            return $model->gui_numero;
        }else {
            return $model->gui_ordcom;
        }
    }

    public function nroReferencia2($model): string
    {
        return $model->gui_tipgui;
    }

    public function codCliente($model): ?string
    {
        return 120320;
    }

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->gui_fechag, 'Y-m-d');
    }

    public function codDepositoOrigen($model): ?string
    {
        return $model->cmdetgui->first()->gui_bodori;
    }

    public function ordenSalidaDetalle($model): Collection
    {
        return  $model->cmdetgui->map(function ($model) {
            $detalle = new class($model) extends OrdenSalidaDetalleService
            {
                protected function codDeposito($model): string
                {
                    Log::info('Relación cmdetgui:', ['cmdetgui' => $model->cmdetgui]);
                    return $model->gui_bodori;
                }

                public function nroOrdenSalida($model): string
                {
                    return $model->gui_numero;
                }

                public function codItem($model): string
                {
                    return $model->gui_produc;
                }

                public function cantidad($model): float
                {
                    return $model->gui_canrep;
                }
            };

            return $detalle->get();
        });
    }

}
