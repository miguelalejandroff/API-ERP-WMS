<?php

namespace App\WMS;

use App\Libs\WMS;
use App\WMS\Contracts\ClienteService;
use App\WMS\Contracts\ItemClaseService;
use App\WMS\Contracts\ItemCodigoBarraService;
use App\WMS\Contracts\ItemService;
use App\WMS\Contracts\OrdenEntradaService;
use App\WMS\Contracts\ProveedorService;
use Illuminate\Http\Request;

/**
 * Clase EndpointWMS: Proporciona métodos para interactuar con el servicio WMS (Warehouse Management System).
 * Permite realizar operaciones como la creación de ítems, clases de ítems, códigos de barras, clientes, proveedores y órdenes de entrada.
 */
class EndpointWMS
{
    /**
     * Constructor de la clase EndpointWMS
     * 
     * @param Request $request Objeto Request
     */
    public function __construct(public Request $request)
    {
    }

    /**
     * Crea un nuevo ítem
     * 
     * @param ItemService $item Objeto ItemService
     * @return mixed Respuesta de la solicitud WMS
     */
    public function createItem(ItemService $item)
    {
        dd($item->getJson());
        return WMS::post('WMS_Admin/CreateItem', $item->getJson());
    }

    /**
     * Crea una nueva clase de ítem
     * 
     * @param ItemClaseService $item Objeto ItemClaseService
     * @return mixed Respuesta de la solicitud WMS
     */
    public function createItemClase(ItemClaseService $item)
    {
        return WMS::post('WMS_Admin/CreateItemClase', $item->getJson());
    }

    /**
     * Crea un nuevo código de barras para un ítem
     * 
     * @param ItemCodigoBarraService $item Objeto ItemCodigoBarraService
     * @return mixed Respuesta de la solicitud WMS
     */
    public function createItemCodigoBarra(ItemCodigoBarraService $item)
    {
        return WMS::post('WMS_Admin/CreateItemCodigoBarra', $item->getJson());
    }

    /**
     * Crea un nuevo cliente
     * 
     * @param ClienteService $item Objeto ClienteService
     * @return mixed Respuesta de la solicitud WMS
     */
    public function createCliente(ClienteService $item)
    {
        return WMS::post('WMS_Admin/CreateCliente', $item->getJson());
    }

    /**
     * Crea un nuevo proveedor
     * 
     * @param ProveedorService $item Objeto ProveedorService
     * @return mixed Respuesta de la solicitud WMS
     */
    public function createProveedor(ProveedorService $item)
    {
        return WMS::post('WMS_Admin/CreateProveedor', $item->getJson());
    }

    /**
     * Crea una nueva orden de entrada
     * 
     * @param OrdenEntradaService $orden Orden de entrada
     * @return mixed Respuesta de la solicitud WMS
     */
    public function createOrdenEntrada(OrdenEntradaService $orden)
    {
        //dd($orden->getJson()->getContent());
        return WMS::post('WMS_Inbound/CreateOrdenEntrada', $orden->getJson());
    }
}
