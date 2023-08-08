<?php

namespace App\ERP\Adapters\OrdenSalida;

use App\ERP\Contracts\OrdenSalidaService;
use App\ERP\Handler\GuiaCompraHandler;
use Exception;
use Illuminate\Support\Facades\DB;

class SolicitudDespacho implements OrdenSalidaService
{
    protected $handlers = [];
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
        $this->handlers = [
            //new GuiaCompraHandler(),
        ];
    }
    public function run()
    {
        //DB::beginTransaction();
        try {
            foreach ($this->handlers as $handler) {
                //$handler->execute($this->context);
            }

            return response()->json(["message" => "Proceso de Despacho sin Problemas"], 200);
        } catch (Exception $e) {
            //DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
