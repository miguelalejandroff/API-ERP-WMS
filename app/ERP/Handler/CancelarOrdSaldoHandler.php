<?php

namespace App\ERP\Handler;

use App\ERP\Context\OrdenEntradaContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelarOrdSaldoHandler
{
    /**
     * Actualiza los saldos de la orden de compra desde WMS.
     *
     * @param OrdenEntradaContext $context
     * @return array
     */
    public function actualizarDesdeWMS(OrdenEntradaContext $context): array
    {
        DB::beginTransaction();

        try {
            // Procesar cantidades recepcionadas por producto
            $cantidadesPorProducto = $this->procesarCantidades($context);

            // Actualizar saldos de la orden de compra
            $this->actualizarSaldos($context, $cantidadesPorProducto);

            DB::commit();

            Log::info('CancelarOrdSaldoHandler', ['message' => 'Inventario actualizado correctamente']);
            return ['success' => true, 'message' => 'Inventario actualizado correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en CancelarOrdSaldoHandler', [
                'message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString()
            ]);
            return ['success' => false, 'message' => 'Error al actualizar desde WMS'];
        }
    }

    /**
     * Procesa las cantidades recepcionadas agrupadas por producto.
     *
     * @param OrdenEntradaContext $context
     * @return array
     */
    private function procesarCantidades(OrdenEntradaContext $context): array
    {
        $cantidadesPorProducto = [];

        $context->solicitudRecepcion->iterarDetalle(function ($detalleSolicitud) use (&$cantidadesPorProducto) {
            $producto = $detalleSolicitud->gui_produc;
            $cantidad = $detalleSolicitud->gui_canrep;

            // Acumular cantidades por producto
            $cantidadesPorProducto[$producto] = ($cantidadesPorProducto[$producto] ?? 0) + $cantidad;
        });

        Log::info('Cantidades agrupadas por producto', $cantidadesPorProducto);
        return $cantidadesPorProducto;
    }

    /**
     * Actualiza los saldos de la orden de compra.
     *
     * @param OrdenEntradaContext $context
     * @param array $cantidadesPorProducto
     */
    private function actualizarSaldos(OrdenEntradaContext $context, array $cantidadesPorProducto): void
    {
        $context->ordenCompra->iterarDetalle(function ($detalle) use ($context, $cantidadesPorProducto) {
            $producto = $detalle->ord_produc;

            if (isset($cantidadesPorProducto[$producto])) {
                $cantidad = $cantidadesPorProducto[$producto];

                $criteriosBusqueda = [
                    'ord_numcom' => $detalle->ord_numcom,
                    'ord_produc' => $producto
                ];

                // Actualizar saldo
                $context->ordenCompra->guardarDetalle(function ($detalleCompra) use ($cantidad) {
                    $detalleCompra->ord_saldos += $cantidad;
                }, $criteriosBusqueda);

                // Cambiar el estado del documento
                $context->ordenCompra->guardarDocumento(function ($documento) {
                    $documento->ord_estado = 'P';
                }, $criteriosBusqueda);

                Log::info('Saldo actualizado para producto', [
                    'producto' => $producto,
                    'cantidad_sumada' => $cantidad
                ]);
            }
        });
    }
}
