<?php

namespace App\ERP\Adapters\OrdenEntrada;

use App\ERP\Build\Adapter;
use App\ERP\Contracts\ERPOrdenEntradaService;
use App\Libs\GuiaCompra;
use App\Libs\GuiaRecepcion;
use App\Libs\SaldoOrden;
use App\Logs\Log;
use App\Models\cmclientes;
use App\Models\cmordcom;
use App\Models\enlacepromo;
use Exception;
use Illuminate\Support\Facades\DB;

class OrdenCompraRecepcion extends Adapter implements ERPOrdenEntradaService
{
    private $observers = [];
    /*
    public function addObserver(OrdenObserver $observer)
    {
        $this->observers[] = $observer;
    }
    private function notify($ordenCompra)
    {
        foreach ($this->observers as $observer) {
            $observer->handle($ordenCompra);
        }
    }*/
    public function run($recepcion, $recepcionDetalle, $trackingId)
    {
        Log::info('/OrdenCompraRececpcion', json_encode($recepcion), $trackingId);

        return [$recepcion, $recepcionDetalle, $trackingId];
        DB::beginTransaction();
        try {
            //throw new Exception("prueba", 500);
            $ordenCompra = cmordcom::Orden($recepcion->numeroOrden);

            if (!$ordenCompra) {
                throw new Exception("Orden de Compra no Existe: {$ordenCompra->ord_numcom}", 500);
            }

            switch ($ordenCompra->ord_estado) {
                case 'R':
                    throw new Exception("Orden de Compra Recepcionada: {$ordenCompra->ord_numcom}", 500);
                case 'A':
                    throw new Exception("Orden de Compra Anulada: {$ordenCompra->ord_numcom}", 500);
                case 'C':
                    throw new Exception("Orden de Compra Cerrada: {$ordenCompra->ord_numcom}", 500);
                case 'P':

                    $ordenCompraBonificada = null;

                    if ($ordenCompra->cmenlbon?->bon_ordbon) {
                        $ordenCompraBonificada = cmordcom::Orden($ordenCompra->cmenlbon->bon_ordbon);
                    }

                    $proveedor = cmclientes::Cliente($recepcion->codProveedor);

                    $cantidadNormal = 0;
                    $cantidadPromo = 0;

                    foreach ($recepcionDetalle as &$row) {

                        $row->precio = $ordenCompra->buscaProducto($row->codigoProducto)->first()->calculaCosto->precioCalculado;

                        $promocion = enlacepromo::where('codigo_promos', $row->codigoProducto)->where('estado', 'A')->first();

                        // Crear una instancia de la clase SaldoOrden
                        new SaldoOrden($ordenCompra, $ordenCompraBonificada, $row->codigoProducto, $row->cantidadRecepcionada, function ($message) {
                            throw new Exception($message, 500);
                        });

                        if (!$promocion) {
                            $row->promocion = false;
                            $cantidadNormal++;
                            continue;
                        }

                        $row->promocion = true;

                        $cantidadPromo++;
                    }

                    new GuiaCompra($recepcion, $recepcionDetalle, $ordenCompra, $proveedor, function ($message) {
                        throw new Exception($message, 500);
                    });

                    new GuiaRecepcion($recepcion, $recepcionDetalle, $ordenCompra, $proveedor, $cantidadPromo, $cantidadNormal, function ($message) {
                        throw new Exception($message, 500);
                    });

                    if ($ordenCompra->cmdetord->sum('ord_saldos') == 0) {
                        $ordenCompra->update(['ord_estado' => "R"]);
                    }
            }
            DB::commit();
            Log::info('/OrdenCompraRececpcion', "Proceso de Recepcion sin problemas", $trackingId);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('/OrdenCompraRececpcion', $e->getMessage(), $trackingId);
            die();
        }
    }
}
