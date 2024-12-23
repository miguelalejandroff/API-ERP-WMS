<?php

namespace App\ERP\Adapters\OrdenSalida;

use App\ERP\Contracts\OrdenSalidaService;
use Exception;


class Pedidos implements OrdenSalidaService
{
    protected $handlers = [];
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
        $this->handlers = [
        ]; 
    }
    public function run()
    {
        //DB::beginTransaction();
        try {
            foreach ($this->handlers as $handler) {
                //$handler->execute($this->context);
            }

            return response()->json(["message" => "Proceso de Pedidos sin Problemas"], 200);
        } catch (Exception $e) {
            //DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}

//Preguntar a Cesar si es solo la generacion de GuiaDespacho
