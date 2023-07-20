<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Build\Adapter;
use App\ERP\Contracts\ERPOrdenEntradaService;
use App\ERP\Handler\GuiaCompraHandler;
use App\Models\cmclientes;
use Illuminate\Support\Facades\DB;

class SolicitudRecepcion
{
    protected $handlers = [];

    public function __construct()
    {
        $this->handlers = [
            new GuiaCompraHandler(),
        ];
    }
    public function run($model)
    {
        DB::beginTransaction();
        try {
            /*$context = new Context($ordenCompra);

            foreach ($this->handlers as $handler) {
                $handler->execute($context);
            }*/
        } catch (\Exception $e) {
            DB::rollBack();
        }
    }
}
