<?php

namespace App\WMS\Contracts;

use App\WMS\Templates\OrdenSalidaOMS;

interface WMSOrdenSalidaOMSService
{
    public function get($model = null);
    public function makeOrdenSalidaOMS($model): OrdenSalidaOMS;
}
