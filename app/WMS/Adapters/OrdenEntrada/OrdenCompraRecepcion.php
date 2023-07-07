<?php

namespace App\WMS\Adapters\OrdenEntrada;

use App\Libs\CalcularCosto;
use App\Libs\Descuentos;
use App\Libs\WMS;
use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSClienteService;
use App\WMS\Contracts\WMSItemService;
use App\WMS\Contracts\WMSOrdenEntradaService;
use App\WMS\Contracts\WMSProveedorService;
use App\WMS\Templates\OrdenEntrada;
use App\WMS\Templates\OrdenEntradaDetalle;
use Carbon\Carbon;

class OrdenCompraRecepcion extends Adapter implements WMSOrdenEntradaService
{
    /**
     * La orden de compra cuando va ser solo informativa se enviara al WMS
     * cual es la bodega especifica hay que preguntar 
     * las ordenes de compra de veterinaria sera bodega = 2
     * las ordenes de compra de matriz seran = 56
     */
    public function makeCodDeposito($model)
    {
        return "02"; // $model->sucursalpororden?->oc_bodega,
    }
    public function makeNroOrdenEntrada($model)
    {
        return $model->ord_numcom;
    }
    public function makeOrdenEntrada($model, WMSProveedorService $proveedor, WMSClienteService $cliente, WMSItemService $item): OrdenEntrada
    {

        return new OrdenEntrada(
            codDeposito: "02", //$model->sucursalpororden?->oc_bodega,
            nroOrdenEntrada: $model->ord_numcom,
            nroReferencia: $model->cmenlbon?->bon_ordbon,
            nroOrdenCliente: "07",
            codTipo: 1,
            codProveedor: $model->ord_subcta,
            codCliente: null,
            codSucursal: null,
            tipoDeCambio: 0,
            fechaEstimadaRecepcion: WMS::now(),
            fechaExpiracion: WMS::nowYear(),
            fechaEmisionERP: WMS::date($model->ord_fechac, 'Y-m-d'),
            codDepositoOrigen: null,
            codDepositoOrigen2: null,
            observacion: $model->cmordobs?->ord_observ,
            origen: null,
            requiereVas: null,
            esCrossDocking: null,
            nroCrossDocking: null,
            codMoneda: null,
            ordenEntradaDetalle: $this->ordenEntradaDetalle($model, $item),
            proveedor: $proveedor->get($model->cmclientes)['proveedor'],
            cliente: [],
        );
    }
    public function ordenEntradaDetalle($model, WMSItemService $item): array
    {
        foreach ($model->cmdetord as $key => $row) {

            $arr[] = new OrdenEntradaDetalle(
                codDeposito: "02", //$model->sucursalpororden?->oc_bodega,
                nroOrdenEntrada: $model->ord_numcom,
                nroLinea: $key + 1,
                codItem: $row->ord_produc,
                codMoneda: $row->cmordcom->ord_moneda,
                cantidadSolicitada: $row->calculaCosto->saldoCalculado,
                item: $item->get($row->cmproductos)['item'][0]
            );
        }
        return $this->arrayObject($arr);
    }
}
