<?php

namespace App\WMS\Adapters;

use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSOrdenDespachoService;
use App\WMS\Templates\OrdenDespacho;

class CreateOrdenDespacho extends Adapter implements WMSOrdenDespachoService
{
    public function makeOrdenDespacho($model): OrdenDespacho
    {
        return new OrdenDespacho(
            codDeposito: "",
            nroOrdenSalida: "",
            tipoDespacho: "",
            tipoEntrega: "",
            codCourier: "",
            nomCourier: "",
            contactoDespacho: "",
            direccionDespacho: "",
            telefonoDespacho: "",
            correoDespacho: "",
            comunaDespacho: "",
            ciudadDespacho: "",
            regionDespacho: "",
            paisDespacho: "",
            rutaDespacho: "",
            ventanaDespacho: "",
            horarioDespacho: "",
            observacionDespacho: "",
            fechaCompromisoDespacho: ""
        );
    }
}
