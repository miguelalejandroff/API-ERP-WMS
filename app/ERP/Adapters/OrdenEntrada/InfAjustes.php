<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Contracts\CancelarDocumentoService;
use App\ERP\Handler\InfAjustesHandler;
use Illuminate\Support\Facades\DB;
use Exception;

class InfAjustes implements CancelarDocumentoService
{
    protected $context;
    protected $handler;

    public function __construct($context)
    {
        $this->context = $context;
        $this->handler = new InfAjustesHandler();
    }

    public function run()
    {
        DB::beginTransaction();

        try {
            $result = $this->handler->actualizarDesdeWMS($this->context);

            if ($result['success']) {
                DB::commit();
                return response()->json(["message" => "Proceso de cancelación de saldo sin problemas"], 200);
            } else {
                DB::rollBack();
                return response()->json(["message" => $result['message']], 500);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(["message" => $e->getMessage()], 500);
        }
    }
}
