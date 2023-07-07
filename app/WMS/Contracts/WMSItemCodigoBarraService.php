<?php

namespace App\WMS\Contracts;

use App\Models\cmproductos;

interface WMSItemCodigoBarraService
{
    public function get($model = null);
    public function makeItemCodigoBarra(cmproductos $model): array;
}
