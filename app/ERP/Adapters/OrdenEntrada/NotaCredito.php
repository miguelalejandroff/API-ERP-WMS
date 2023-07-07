<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\Enums\SaldoBodegaEnum;
use App\ERP\Build\Adapter;
use App\ERP\Contracts\ERPOrdenEntradaService;
use App\Libs\SaldoBodega;
use App\Logs\Log;
use Exception;
use Illuminate\Support\Facades\DB;

class NotaCredito extends Adapter implements ERPOrdenEntradaService
{
    public function run($recepcion, $recepcionDetalle, $trackingId)
    {
        Log::info('/OrdenCompraRececpcion', json_encode($recepcion), $trackingId);
        return [$recepcion, $recepcionDetalle, $trackingId];
        DB::beginTransaction();
        try {

            foreach ($recepcionDetalle as $key => $row) {

                new SaldoBodega($recepcion->bodegaDestino, $row->codigoProducto, $row->cantidadRecepcionada, SaldoBodegaEnum::INCREMENT, function ($message) {
                    throw new Exception($message, 500);
                });
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('/NotaCredito', $e->getMessage());
            die();
        }
    }
}
