<?php

namespace App\ERP\Handler;

use App\ERP\Context\OrdenEntradaContext;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Log;

class CancelarOrdSaldoHandler
{
    public function actualizarDesdeWMS(OrdenEntradaContext $context)
    {
        DB::beginTransaction();

        try {
            // Variables para almacenar las cantidades de gui_canrep asociadas a cada ord_produc
            $ordProducCantidad = [];

            // Iterar sobre los detalles de la solicitud de recepción
            $context->solicitudRecepcion->iterarDetalle(function ($detalleSolicitud) use ($context, &$ordProducCantidad) {
                // Obtener la cantidad de gui_canrep para este detalle de solicitud
                $cantidad = $detalleSolicitud->gui_canrep;
                // Obtener el ord_produc correspondiente al gui_produc
                $ordProduc = $detalleSolicitud->gui_produc;

                // Sumar la cantidad al arreglo de cantidades asociadas al ord_produc
                if (!isset($ordProducCantidad[$ordProduc])) {
                    $ordProducCantidad[$ordProduc] = 0;
                }
                $ordProducCantidad[$ordProduc] += $cantidad;
            });

            // Iterar sobre los detalles de la orden de compra y actualizar ord_saldos si hay coincidencia con ord_produc y gui_produc
            $context->ordenCompra->iterarDetalle(function ($detalle) use ($context, $ordProducCantidad) {
                // Verificar si ord_produc está presente en las cantidades asociadas a gui_produc
                if (isset($ordProducCantidad[$detalle->ord_produc])) {
                    // Obtener la cantidad correspondiente
                    $cantidad = $ordProducCantidad[$detalle->ord_produc];

                    // Crear los criterios de búsqueda
                    $criteriosBusqueda = [
                        'ord_numcom' => $detalle->ord_numcom,
                        'ord_produc' => $detalle->ord_produc
                    ];

                    // Actualizar ord_saldos sumando la cantidad correspondiente
                    $context->ordenCompra->guardarDetalle(
                        function ($detalleCompra) use ($cantidad) {
                            $detalleCompra->ord_saldos += $cantidad;
                        },
                        $criteriosBusqueda
                    );
                    $context->ordenCompra->guardarDocumento(
                        function ($documento){
                            $documento->ord_estado = 'P';
                        },
                        $criteriosBusqueda
                    );
                }
            });

            DB::commit();

            return ['success' => true, 'message' => 'Inventario actualizado correctamente'];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            Log::error('Error al actualizar desde WMS:', ['stack_trace' => $e->getTrace()]);
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
