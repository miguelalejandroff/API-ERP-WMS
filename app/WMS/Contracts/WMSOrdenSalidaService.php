<?php

namespace App\WMS\Contracts;

use App\WMS\Templates\OrdenSalida;

interface WMSOrdenSalidaService
{
    public function get($model = null);
    public function makeCodDeposito($model);
    public function makeNroOrdenSalida($model);
    public function makeOrdenSalida($model, WMSOrdenDespachoService $despacho, WMSClienteService $cliente): OrdenSalida;
    public function ordenSalidaDetalle($model): array;
}
