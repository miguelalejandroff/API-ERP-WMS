<?php

namespace App\WMS\Adapters;

use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSOrdenFacturacionService;
use App\WMS\Templates\OrdenFacturacion;

class CreateOrdenFacturacion extends Adapter implements WMSOrdenFacturacionService
{
    public function makeOrdenFacturacion($model): OrdenFacturacion
    {
        return new OrdenFacturacion(
            codDeposito: "",
            nroOrdenSalida: "",
            folioFacturacion: 0,
            tipoFacturacion: "",
            direccionFacturacion: "",
            telefonoFacturacion: "",
            correoFacturacion: "",
            comunaFacturacion: "",
            ciudadFacturacion: "",
            regionFacturacion: "",
            paisFacturacion: "",
            giroFacturacion: "",
            observacionFacturacion: ""
        );
    }
}
