<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\WMS;
use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSClienteService;
use App\WMS\Contracts\WMSItemService;
use App\WMS\Contracts\WMSOrdenEntradaService;
use App\WMS\Contracts\WMSProveedorService;
use App\WMS\Templates\OrdenEntrada;
use App\WMS\Templates\OrdenEntradaDetalle;

class Guia extends Adapter implements WMSOrdenEntradaService
{
    public function makeCodDeposito($model)
    {
        return "01"; // $model->fac_codbod,
    }
    public function makeNroOrdenEntrada($model)
    {
        return $model->gui_numero;
    }
    public function makeOrdenEntrada($model, WMSProveedorService $proveedor, WMSClienteService $cliente, WMSItemService $item): OrdenEntrada
    {
        return new OrdenEntrada(
            codDeposito: "01", // $model->fac_codbod,
            nroOrdenEntrada: $model->gui_numero,
            nroReferencia: null,
            nroOrdenCliente: null,
            codTipo: 4,
            codProveedor: null,
            codCliente: null,
            codSucursal: null,
            tipoDeCambio: 0,
            fechaEstimadaRecepcion: WMS::now(),
            fechaExpiracion: WMS::now(),
            fechaEmisionERP: WMS::date($model->gui_fechag),
            codDepositoOrigen: null,
            codDepositoOrigen2: null,
            observacion: null,
            origen: null,
            requiereVas: null,
            esCrossDocking: null,
            nroCrossDocking: null,
            codMoneda: null,
            ordenEntradaDetalle: $this->ordenEntradaDetalle($model, $item),
            proveedor: [],
            cliente: [],
        );
    }
    public function ordenEntradaDetalle($model, WMSItemService $item): array
    {
        foreach ($model->cmdetgui as $key => $row) {
            $arr[] = new OrdenEntradaDetalle(
                codDeposito: "01", //$row->gui_boddes,
                nroOrdenEntrada: $model->gui_numero,
                nroLinea: $key + 1,
                codItem: $row->gui_produc,
                codMoneda: 1,
                cantidadSolicitada: $row->gui_canord,
                item: $item->get($row->cmproductos)['item'][0]
            );
        }
        return $this->arrayObject($arr);
    }
}
