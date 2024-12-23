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
    public function nroOrdenCliente($model): string
    {
        if($model->des_tipo == '16') {
            return $model->des_facals;
        }else {
            return $model->des_folio;
        }
    }

    protected function tipoOrdenSalida($model): int
    {
        if($model->des_tipo == '16' || $model->des_tipo == '48') {
            return 13;
        }else {
            return 2;
        }
    }

    public function nroReferencia($model): string
    {
        return $model->des_folio;
    }

    public function nroReferencia2($model): string
    {
        return $model->des_tipo;
    }

    public function codCliente($model): ?string
    {
        if($model->des_tipo == '16' || $model->des_tipo == '48') {
            return $model->des_subcta;
        }else {
            return 120320;
        }
    }
    //public function codSucursal($model): ?string
    //{
        //return $model->despachodetalle->first()->des_bodori;
    //}

    public function fechaEmisionERP($model): ?string
    {
        return  WMS::date($model->des_fecha, 'Y-m-d');
    }

    public function observacion($model): string
    {
        return "";
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

                public function cantidad($model): float
                {
                    return $model->des_stockp;
                }
            };

            return $detalle->get();
        });
    }
    public function cliente($model) //distinto de 16. Para 16 es des_subcta
    {
        if($model->des_tipo == '16' || $model->des_tipo == '48' || $model->des_tipo == '39') {
            return (new CreateCliente($model->cmclientes ?? cmclientes::where('aux_claves', $model->des_subcta)->first()))->get(); 
        }else {
            return (new CreateCliente($model->cmclientes ?? cmclientes::where('aux_claves', 120320)->first()))->get(); 
        }
    }
        
        
}
