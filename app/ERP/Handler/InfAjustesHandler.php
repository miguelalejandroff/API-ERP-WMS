<?php

namespace App\ERP\Handler;

use App\ERP\Context\OrdenEntradaContext;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InfAjustesHandler
{
    /**
     * Actualiza las cantidades de inventario desde WMS.
     *
     * @param OrdenEntradaContext $context
     * @return array
     */
    public function actualizarDesdeWMS(OrdenEntradaContext $context): array
    {
        DB::beginTransaction();

        try {
            $cantidadesPorProducto = $this->procesarDetallesSolicitud($context);

            $this->actualizarOrdenCompra($context, $cantidadesPorProducto);

            DB::commit();

            return ['success' => true, 'message' => 'Inventario actualizado correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
            ]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Procesa los detalles de la solicitud de recepción y agrupa cantidades por producto.
     *
     * @param OrdenEntradaContext $context
     * @return array
     */
    private function procesarDetallesSolicitud(OrdenEntradaContext $context): array
    {
        $cantidadesPorProducto = [];

        $context->guiaRecepcion->iterarDetalle(function ($detalleSolicitud) use (&$cantidadesPorProducto) {
            $producto = $detalleSolicitud->gui_produc;
            $cantidad = $detalleSolicitud->gui_canord;

            // Agrupar cantidades por producto
            $cantidadesPorProducto[$producto] = ($cantidadesPorProducto[$producto] ?? 0) + $cantidad;
        });

        Log::info('Cantidades procesadas desde la solicitud', $cantidadesPorProducto);

        return $cantidadesPorProducto;
    }

    /**
     * Actualiza los detalles de la orden de compra con base en las cantidades procesadas.
     *
     * @param OrdenEntradaContext $context
     * @param array $cantidadesPorProducto
     */
    private function actualizarOrdenCompra(OrdenEntradaContext $context, array $cantidadesPorProducto): void
    {
        $context->ordenCompra->iterarDetalle(function ($detalleOrden) use ($context, $cantidadesPorProducto) {
            $producto = $detalleOrden->ord_produc;

            if (isset($cantidadesPorProducto[$producto])) {
                $cantidad = $cantidadesPorProducto[$producto];

                $criteriosBusqueda = [
                    'ord_numcom' => $detalleOrden->ord_numcom,
                    'ord_produc' => $producto
                ];

                $context->ordenCompra->guardarDetalle(
                    function ($detalleCompra) use ($cantidad) {
                        $detalleCompra->ord_saldos += $cantidad;
                    },
                    $criteriosBusqueda
                );

                Log::info('Orden de compra actualizada', [
                    'producto' => $producto,
                    'cantidad_sumada' => $cantidad,
                    'nuevo_saldo' => $detalleOrden->ord_saldos + $cantidad
                ]);
            }
        });
    }
}
