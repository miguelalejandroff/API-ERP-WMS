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

class NotaCredito extends Adapter implements WMSOrdenEntradaService
{
    public function makeCodDeposito($model)
    {
        return "01"; // $model->fac_codbod,
    }
    public function makeNroOrdenEntrada($model)
    {
        return $model->fac_nrodoc;
    }
    public function makeOrdenEntrada($model, WMSProveedorService $proveedor, WMSClienteService $cliente, WMSItemService $item): OrdenEntrada
    {
        return new OrdenEntrada(
            codDeposito: "01", // $model->fac_codbod,
            nroOrdenEntrada: $model->fac_nrodoc,
            nroReferencia: null,
            nroOrdenCliente: null,
            codTipo: 5,
            codProveedor: null,
            codCliente: $model->fac_subcta,
            codSucursal: null,
            tipoDeCambio: 0,
            fechaEstimadaRecepcion: WMS::now(),
            fechaExpiracion: WMS::now(),
            fechaEmisionERP: WMS::date($model->fac_fecdoc, 'Y-m-d'),
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
            cliente: $cliente->get($model->cmclientes)['cliente'],
        );
    }
    public function ordenEntradaDetalle($model, WMSItemService $item): array
    {
        foreach ($model->cmfacdet as $key => $row) {
            $arr[] = new OrdenEntradaDetalle(
                codDeposito: "01", //$model->sucursalpororden?->oc_bodega,
                nroOrdenEntrada: $model->fac_nrodoc,
                nroLinea: $key + 1,
                codItem: $row->fac_codpro,
                codMoneda: 1,
                cantidadSolicitada: $row->fac_cantid,
                item: $item->get($row->cmproductos)['item'][0]
            );
        }
        return $this->arrayObject($arr);
    }
}
