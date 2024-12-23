<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\Libs\TipoDocumentoWMS;
use App\ERP\Enum\TipoDocumentoERP;

use App\Libs\WMS;
use App\Models\cmclientes;
use App\WMS\Adapters\Admin\CreateCliente;
use App\WMS\Contracts\Outbound\OrdenSalidaDetalleService;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use Illuminate\Support\Collection;

class PedidoKDX extends OrdenSalidaService
{
    protected function codDeposito($model): string
    {
        return $model->cmdetgui->first()->gui_bodori;
    }
    protected function nroOrdenSalida($model): string
    {
        return $model->gui_numero;
    }
    public function nroOrdenCliente($model): string
    {
        return $model->folioPedido->ped_folio;
    }

    protected function tipoOrdenSalida($model): int
    {
        return 5;
    }

    public function nroReferencia($model): string
    {
        return $model->folioPedido->ped_folio;
    }

    public function nroReferencia2($model): string
    {
        return $model->gui_tipgui;
    }

    public function codCliente($model): ?string
    {
        return 120320;
    }
    public function codSucursal($model): ?string
    {
        return $model->des_sucori;
    }

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->gui_fechag, 'Y-m-d');
    }

    public function observacion($model): string
    {
        return "";
    }

    public function ordenSalidaDetalle($model): Collection
    {
        return  $model->cmdetgui->map(function ($model) {
            $detalle = new class($model) extends OrdenSalidaDetalleService
            {
                protected function codDeposito($model): string
                {
                    return $model->gui_bodori;
                }

                protected function nroOrdenSalida($model): string
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
