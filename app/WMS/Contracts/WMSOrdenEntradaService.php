<?php

namespace App\WMS\Contracts;

use App\WMS\Templates\OrdenEntrada;

interface WMSOrdenEntradaService
{
    public function get($model = null);
    public function makeCodDeposito($model);
    public function makeNroOrdenEntrada($model);
    public function makeOrdenEntrada($model, WMSProveedorService $proveedor, WMSClienteService $cliente, WMSItemService $item): OrdenEntrada;
    public function ordenEntradaDetalle($model, WMSItemService $item): array;
}
