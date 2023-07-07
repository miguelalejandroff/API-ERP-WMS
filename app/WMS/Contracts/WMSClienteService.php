<?php

namespace App\WMS\Contracts;

use App\Models\cmclientes;
use App\WMS\Templates\Cliente;

interface WMSClienteService
{
    public function get($model = null);
    public function makeCliente(cmclientes $model): Cliente;
}
