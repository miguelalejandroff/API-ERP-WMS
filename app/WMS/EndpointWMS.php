<?php

namespace App\WMS;

use App\Libs\WMS;
use App\Models\cmordcom;
use App\WMS\Adapters\OrdenEntrada\OrdenCompraRecepcion;
use App\WMS\Contracts\WMSItemService;
use App\WMS\Contracts\WMSOrdenEntradaService;
use App\WMS\Templates\Abstracts\ItemService;
use Illuminate\Http\Request;

class EndpointWMS
{
    public function __construct(public Request $request)
    {
    }
    public function createItem(ItemService $item)
    {
        return WMS::post('WMS_Admin/CreateItem', $item->getJson());
    }
    public function createOrdenEntrada(WMSOrdenEntradaService $orden)
    {
        #return $orden->get();
        return WMS::post('WMS_Inbound/CreateOrdenEntrada', $orden->get());
    }
}
