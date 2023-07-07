<?php

namespace App\WMS\Adapters\OrdenSalida;

use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSClienteService;
use App\WMS\Contracts\WMSOrdenDespachoService;
use App\WMS\Contracts\WMSOrdenFacturacionService;
use App\WMS\Contracts\WMSOrdenSalidaOMSService;
use App\WMS\Contracts\WMSOrdenSalidaService;
use App\WMS\Templates\OrdenSalida;

class SolicitudGuia extends Adapter implements WMSOrdenSalidaService
{
    public function makeCodDeposito($model)
    {
        return "01";
    }
    public function makeNroOrdenSalida($model)
    {
        return $model->des_folio;
    }
    public function makeOrdenSalida($model, WMSOrdenDespachoService $despacho,  WMSClienteService $cliente): OrdenSalida
    {
        return new OrdenSalida(
            codDeposito: "01",
            nroOrdenSalida: $model->des_folio,
            nroOrdenCliente: "",
            tipoOrdenSalida: 3,
            nroReferencia: "",
            codMoneda: 1,
            codCliente: "",
            codSucursal: "",
            fechaEmisionERP: "",
            fechaCompromiso: "",
            observacion: "",
            prioridad: "",
            codDepositoDespacho: "",
            codDepositoDespacho2: "",
            ordenDespacho: $despacho->get($model)['ordenDespacho'],
            ordenSalidaDetalle: $this->ordenSalidaDetalle($model),
            cliente: [], //$cliente->get($model->cmclientes)["cliente"],
        );
    }
    public function ordenSalidaDetalle($model): array
    {
        return [];
    }
}
