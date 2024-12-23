<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Handler\GuiaCompraHandler;
use App\ERP\Handler\GuiaRecepcionHandler;
use App\ERP\Handler\MaestroProductoHandler;
use Exception;
use Illuminate\Support\Facades\DB;
use APP\Logs\Log;

class ActualizarProductosContext
{
    public $productosParaActualizar;

    public function __construct($productosParaActualizar)
    {
        $this->productosParaActualizar = ($productosParaActualizar);
    }
}