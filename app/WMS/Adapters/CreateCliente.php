<?php

namespace App\WMS\Adapters;

use App\Models\cmclientes;
use App\WMS\Build\Adapter;
use App\WMS\Contracts\WMSClienteService;
use App\WMS\Templates\Cliente;

class CreateCliente extends Adapter implements WMSClienteService
{
    public function makeCliente(cmclientes $model): Cliente
    {
        return new Cliente(
            codCliente:  $model->aux_claves,
            rutCliente:  "{$model->aux_numrut}-{$model->aux_digrut}",
            nomCliente:  $model->aux_nombre,
            razonSocial:  null,
            nomCorto: null,
            giroCliente: $model->aux_nacion,
            direccion: $model->aux_direcc,
            comuna: $model->comuna,
            ciudad: $model->ciudad,
            pais: null,
            localidad: null,
            telefono: $model->aux_telefo,
            eMail: null,
            contacto: null,
            fillRate: null,
            codTipo: 0,
            b2B: "",
            arraySucursal: [],
        );
    }
}
