<?php

namespace App\WMS;

use App\Libs\WMS;
use App\WMS\Contracts\ItemService;
use Illuminate\Http\Request;

class EndpointWMS
{
    public function __construct(public Request $request)
    {
    }
    public function createItem(ItemService $item)
    {
        dd($item->getJson());
        return WMS::post('WMS_Admin/CreateItem', $item->getJson());
    }
    public function createOrdenEntrada($orden)
    {
        return WMS::post('WMS_Inbound/CreateOrdenEntrada', $orden->get());
    }
}
