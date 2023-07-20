<?php

namespace App\WMS;

use App\Libs\WMS;
use App\WMS\Contracts\ClienteService;
use App\WMS\Contracts\ItemClaseService;
use App\WMS\Contracts\ItemCodigoBarraService;
use App\WMS\Contracts\ItemService;
use App\WMS\Contracts\ProveedorService;
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

    public function createItemClase(ItemClaseService $item)
    {
        return WMS::post('WMS_Admin/CreateItemClase', $item->getJson());
    }

    public function createItemCodigoBarra(ItemCodigoBarraService $item)
    {
        return WMS::post('WMS_Admin/CreateItemCodigoBarra', $item->getJson());
    }

    public function createCliente(ClienteService $item)
    {
        return WMS::post('WMS_Admin/CreateCliente', $item->getJson());
    }

    public function createProveedor(ProveedorService $item)
    {
        return WMS::post('WMS_Admin/CreateProveedor', $item->getJson());
    }
    
    public function createOrdenEntrada($orden)
    {
        return WMS::post('WMS_Inbound/CreateOrdenEntrada', $orden->get());
    }
}
