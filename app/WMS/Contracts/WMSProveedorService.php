<?php

namespace App\WMS\Contracts;

use App\Models\cmclientes;
use App\WMS\Templates\Proveedor;

interface WMSProveedorService
{
    public function get($model = null);
    public function makeProveedor(cmclientes $model): Proveedor;
}
