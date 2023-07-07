<?php

namespace App\WMS\Jobs;

use App\Libs\WMS;
use App\Models\cmproductos;
use App\WMS\Templates\Implements\CreateItem;

class ActualizarMaestroProductoWMS
{
    public function __construct()
    {
        $productos = cmproductos::where('pro_anomes', '202306')->get();
        foreach ($productos as $key => $row) {
            $createItem =  new CreateItem($row);
            WMS::post('WMS_Admin/CreateItem', $createItem->getJson());
        }
    }
}
