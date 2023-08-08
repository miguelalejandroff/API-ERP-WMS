<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\Libs\WMS;
use App\Models\cmclientes;
use App\WMS\Adapters\Admin\CreateCliente;
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
    protected function tipoOrdenSalida($model): int
    {
        return 2;
    }
    public function codCliente($model): ?string
    {
        return 120320;
    }
    public function codSucursal($model): ?string
    {
        return 1;
    }
    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->des_fecha, 'Y-m-d');
    }
    public function ordenSalidaDetalle($model): Collection
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

                public function cantidad($model): int
                {
                    return $model->des_stockp;
                }
            };

            return $detalle->get();
        });
    }
    public function cliente($model)
    {
        return (new CreateCliente($model->cmclientes ?? cmclientes::where('aux_claves', 120320)->first()))->get();
    }
}
