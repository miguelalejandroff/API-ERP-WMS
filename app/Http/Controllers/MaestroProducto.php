<?php

namespace App\Http\Controllers;

use App\Libs\WMS;
use App\Models\cmproductos;
use App\WMS\Adapters\CreateItem;

class MaestroProducto extends Controller
{
    public function __construct()
    {
        $productos = cmproductos::where('pro_anomes', '202303')->get();
        $num = 0;
        foreach ($productos as $key => $row) {
            $createItem =  new CreateItem($row);
            WMS::post('WMS_Admin/CreateItem', $createItem->get());
            $num++;
        }
        dd($num);
    }
}
