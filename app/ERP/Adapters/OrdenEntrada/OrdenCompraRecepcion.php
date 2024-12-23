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
use Illuminate\Support\Facades\DB;
use Exception;

class OrdenCompraRecepcion extends Adapter implements ERPOrdenEntradaService
{
    /**
     * Ejecuta el proceso de recepción de orden de compra.
     *
     * @param object $recepcion
     * @param array $recepcionDetalle
     * @param string $trackingId
     * @return void
     */
    public function run($recepcion, $recepcionDetalle, $trackingId)
    {
        DB::beginTransaction();

        try {
            Log::info('/OrdenCompraRececpcion - Inicio', ['trackingId' => $trackingId]);

            // Obtener orden de compra
            $ordenCompra = $this->getOrdenCompra($recepcion->numeroOrden);

            // Procesar orden según su estado
            $this->validarEstadoOrdenCompra($ordenCompra);

            // Procesar recepción
            $this->procesarRecepcion($recepcion, $recepcionDetalle, $ordenCompra);

            // Actualizar estado de la orden si el saldo es cero
            $this->actualizarEstadoOrden($ordenCompra);

            DB::commit();

            Log::info('/OrdenCompraRececpcion - Proceso completado', ['trackingId' => $trackingId]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('/OrdenCompraRececpcion - Error', [
                'message' => $e->getMessage(),
                'trackingId' => $trackingId
            ]);

            throw new Exception("Error en el proceso de recepción de la orden de compra: " . $e->getMessage(), 500);
        }
    }

    /**
     * Obtiene la orden de compra.
     */
    private function getOrdenCompra($numeroOrden)
    {
        $ordenCompra = cmordcom::Orden($numeroOrden);

        if (!$ordenCompra) {
            throw new Exception("Orden de Compra no Existe: {$numeroOrden}");
        }

        return $ordenCompra;
    }

    /**
     * Valida el estado de la orden de compra.
     */
    private function validarEstadoOrdenCompra($ordenCompra)
    {
        $estadosInvalidos = [
            'R' => "Orden de Compra Recepcionada",
            'A' => "Orden de Compra Anulada",
            'C' => "Orden de Compra Cerrada"
        ];

        if (isset($estadosInvalidos[$ordenCompra->ord_estado])) {
            throw new Exception("{$estadosInvalidos[$ordenCompra->ord_estado]}: {$ordenCompra->ord_numcom}");
        }
    }

    /**
     * Procesa la recepción de la orden.
     */
    private function procesarRecepcion($recepcion, $recepcionDetalle, $ordenCompra)
    {
        $proveedor = cmclientes::Cliente($recepcion->codProveedor);
        $cantidadNormal = 0;
        $cantidadPromo = 0;

        foreach ($recepcionDetalle as &$row) {
            $producto = $ordenCompra->buscaProducto($row->codigoProducto)->first();

            $row->precio = $producto->calculaCosto->precioCalculado ?? 0;

            $promocion = enlacepromo::where('codigo_promos', $row->codigoProducto)->first();

            // Procesar saldo de la orden
            new SaldoOrden(
                $ordenCompra,
                null,
                $row->codigoProducto,
                $row->cantidadRecepcionada,
                function ($message) {
                    throw new Exception($message);
                }
            );

            if (!$promocion) {
                $row->promocion = false;
                $cantidadNormal++;
                continue;
            }

            $row->promocion = true;
            $cantidadPromo++;
        }

        // Procesar guías
        new GuiaCompra($recepcion, $recepcionDetalle, $ordenCompra, $proveedor, function ($message) {
            throw new Exception($message);
        });

        new GuiaRecepcion($recepcion, $recepcionDetalle, $ordenCompra, $proveedor, $cantidadPromo, $cantidadNormal, function ($message) {
            throw new Exception($message);
        });
    }

    /**
     * Actualiza el estado de la orden si no hay saldo.
     */
    private function actualizarEstadoOrden($ordenCompra)
    {
        if ($ordenCompra->cmdetord->sum('ord_saldos') == 0) {
            $ordenCompra->update(['ord_estado' => "R"]);
        }
    }
}
