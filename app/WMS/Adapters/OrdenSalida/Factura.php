<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\Libs\WMS;
use App\WMS\Contracts\Outbound\OrdenSalidaDetalleService;
use App\WMS\Contracts\Outbound\OrdenSalidaService;
use Illuminate\Support\Collection;
use App\Models\cmclientes;
use App\WMS\Adapters\Admin\CreateCliente;
use Illuminate\Support\Facades\Log;

class Factura extends OrdenSalidaService
{
    protected function codDeposito($model): string
    {
        return $model->cmfacdet->first()->fac_bodori;
    }
    protected function nroOrdenSalida($model): string
    {
        return $model->fac_nrodoc;
    }

    public function nroOrdenCliente($model): string
    {
        return $model->fac_nrodoc;
    }

    protected function tipoOrdenSalida($model): int
    {
        switch ($model->fac_tipdoc) { 
            case '49':
                return 49;
            case '88':
                return 88;
            case '94':
                return 94;
            case '96':
                return 96;
            case '99':
                return 99;
        }
    }

    public function nroReferencia($model): string
    {
        return $model->fac_nrodoc;
    }

    public function nroReferencia2($model): string
    {
        return $model->fac_tipdoc;
    }

    public function codCliente($model): ?string
    {
        return $model->fac_subcta;
    }

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->fac_fecdoc, 'Y-m-d');
    }

    public function observacion($model): string
    {
        return "";
    }

    public function OrdenSalidaDetalle($model): Collection
    {
        return  $model->cmfacdet->map(function ($model) {
            $detalle = new class($model) extends OrdenSalidaDetalleService
            {
                protected function codDeposito($model): string
                {
                    return $model->fac_bodori;
                }

                protected function nroOrdenSalida($model): string
                {
                    return $model->fac_nrodoc;

                }

                public function codItem($model): string
                {
                    return $model->fac_codpro;
                }

                public function cantidad($model): float
                {
                    return $model->fac_cantid;
                }
            };
            return $detalle->get();
        });
    }

    public function cliente($model) //distinto de 16. Para 16 es des_subcta
    {
        return (new CreateCliente($model->cmclientes ?? cmclientes::where('aux_claves', $model->des_subcta)->first()))->get();
    }

}
