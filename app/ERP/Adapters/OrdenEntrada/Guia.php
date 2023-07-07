<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\Enums\SaldoBodegaEnum;
use App\ERP\Build\Adapter;
use App\ERP\Contracts\ERPOrdenEntradaService;
use App\Libs\Convert;
use App\Libs\SaldoBodega;
use App\Logs\Log;
use Exception;
use Illuminate\Support\Facades\DB;

class Guia extends Adapter implements ERPOrdenEntradaService
{
    public function run($recepcion, $recepcionDetalle, $trackingId)
    {
        Log::info('/OrdenCompraRececpcion', json_encode($recepcion), $trackingId);
        return [$recepcion, $recepcionDetalle, $trackingId];
        DB::beginTransaction();
        try {

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('/NotaCredito', $e->getMessage());
            die();
        }
    }
}
