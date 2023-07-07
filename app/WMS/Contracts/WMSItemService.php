<?php

namespace App\WMS\Contracts;

use App\Models\cmproductos;

interface WMSItemService
{
    public function get($model = null);
    public function makeItem(cmproductos $model, WMSItemCodigoBarraService $codigoBarraService): array;
}
