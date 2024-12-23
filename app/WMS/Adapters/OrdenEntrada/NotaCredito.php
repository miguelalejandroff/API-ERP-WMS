<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Adapters\Admin\CreateProveedor;
use App\WMS\Contracts\Inbound\OrdenEntradaDetalleService;
use App\WMS\Contracts\Inbound\OrdenEntradaService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Exceptions\CustomException;
use App\Casts\CorrelativoRecepcionCast;

class NotaCredito extends OrdenEntradaService
{

    protected function codDeposito($model): string
    {
        return $model->cmfacdet->first()->fac_bodori;
    }

    public function nroOrdenEntrada($model): string
    {
        return $model->fac_nrodoc;
    }
    
    protected function codTipo($model): string
    {
        switch ($model->fac_tipdoc) { 
            case '13':
                return 13;
            case '19':
                return 19;
            case '37':
                return 37;
            case '38':
                return 38;
        }
    }

    public function nroReferencia($model): string
    {
        return $model->regnotac->nrofac;
    }

    public function nroReferencia2($model): string
    {
        return $model->fac_tipdoc;
    }

    public function codCliente($model): string
    {
        return  $model->fac_subcta;
    }

    public function nroOrdenCliente($model): ?string
    {
        return $model->regnotac->nrofac;
    }
    

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->fac_fecdoc, 'Y-m-d');
    }

    public function ordenEntradaDetalle($model): Collection
    {
        return  $model->cmfacdet->map(function ($model) {
            $detalle = new class($model) extends OrdenEntradaDetalleService
            {
                protected function codDeposito($model): string
                {
                    return $model->fac_bodori;
                }

                public function nroOrdenEntrada($model): string
                {
                    return $model->fac_nrodoc;
                }

                public function codItem($model): string
                {
                    return $model->fac_codpro;
                }

                public function cantidadSolicitada($model): float
                {
                    return $model->fac_cantid;
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
