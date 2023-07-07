<?php

namespace App\WMS\Contracts;

use App\WMS\Templates\OrdenDespacho;

interface WMSOrdenDespachoService
{
    public function get($model = null);
    public function makeOrdenDespacho($model): OrdenDespacho;
}
