<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\OrdenEntradaService;
use App\ERP\Handler\GuiaCompraHandler;
use Exception;
use Illuminate\Support\Facades\DB;

class SolicitudRecepcion implements OrdenEntradaService
{
    protected $handlers = [];
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;

        $this->handlers = [
            new GuiaCompraHandler(),
        ];
    }
    public function run()
    {
        DB::beginTransaction();
        try {
            foreach ($this->handlers as $handler) {
                $handler->execute($this->context);
            }
            
            return response()->json(["message" => "Proceso de Recepcion sin Problemas"], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
