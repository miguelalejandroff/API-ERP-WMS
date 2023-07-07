<?php

namespace App\WMS\Adapters;

use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSOrdenSalidaOMSService;
use App\WMS\Templates\OrdenSalidaOMS;

class CreateOrdenSalidaOMS extends Adapter implements WMSOrdenSalidaOMSService
{
    public function makeOrdenSalidaOMS($model): OrdenSalidaOMS
    {
        return new OrdenSalidaOMS(
            codDeposito: "",
            nroOrdenSalida: "",
            nroReferenciaOMS: "",
            nombreOMS: ""
        );
    }
}
