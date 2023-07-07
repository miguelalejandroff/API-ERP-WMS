<?php

namespace App\WMS\Contracts;

use App\WMS\Templates\OrdenFacturacion;

interface WMSOrdenFacturacionService
{
    public function get($model = null);
    public function makeOrdenFacturacion($model): OrdenFacturacion;
}
