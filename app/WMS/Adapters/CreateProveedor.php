<?php

namespace App\WMS\Adapters;

use App\Models\cmclientes;
use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSProveedorService;
use App\WMS\Templates\Proveedor;

class CreateProveedor extends Adapter implements WMSProveedorService
{
    public function makeProveedor(cmclientes $model): Proveedor
    {
        return new Proveedor(
            codProveedor: $model->aux_claves,
            rutProveedor: "{$model->aux_numrut}-{$model->aux_digrut}",
            nomProveedor: $model->aux_nombre,
            razonSocial: null,
            nomCorto: null,
            giroProveedor: $model->aux_nacion,
            direccion: $model->aux_direcc,
            comuna: $model->comuna,
            ciudad: $model->ciudad,
            pais: null,
            localidad: null,
            telefono: $model->aux_telefo,
            eMail: null,
            contacto: null,
            fillRate: null
        );
    }
}
