<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\Libs\WMS;
use App\WMS\Contracts\Outbound\OrdenSalidaDetalleService;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use Illuminate\Support\Collection;

class SolicitudDespacho extends OrdenSalidaService
{
    protected function codDeposito($model): string
    {
        return $model->despachodetalle->first()->des_bodori;
    }
    protected function nroOrdenSalida($model): string
    {
        return $model->des_folio;
    }
    public function nroOrdenCliente($model): ?string
    {
        return $model->des_facals;
    }
    protected function tipoOrdenSalida($model): string
    {
        return 3;
    }
    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->des_fecha, 'Y-m-d');
    }
    public function ordenEntradaDetalle($model): Collection
    {
        return  $model->despachodetalle->map(function ($model) {
            $detalle = new class($model) extends OrdenSalidaDetalleService
            {
                protected function codDeposito($model): string
                {
                    return $model->des_bodori;
                }

                protected function nroOrdenSalida($model): string
                {
                    return $model->des_folio;
                }

                public function codItem($model): string
                {
                    return $model->des_codigo;
                }

                public function cantidadSolicitada($model): int
                {
                    return $model->des_stockp;
                }

            };

            return $detalle->get();
        });
    }
}
