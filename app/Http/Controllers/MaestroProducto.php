<?php

namespace App\Http\Controllers;

use App\Libs\WMS;
use App\Models\cmproductos;
use App\WMS\Adapters\CreateItem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class MaestroProducto extends Controller
{
    /**
     * Procesa y envía productos a la API de WMS.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function enviarProductosAWMS()
    {
        try {
            // Obtener los productos del mes especificado
            $productos = cmproductos::where('pro_anomes', '202303')->get();

            if ($productos->isEmpty()) {
                return response()->json(['message' => 'No se encontraron productos para procesar.'], 404);
            }

            $contador = 0;

            // Procesar los productos en lotes
            DB::beginTransaction();

            foreach ($productos as $producto) {
                try {
                    $createItem = new CreateItem($producto);
                    WMS::post('WMS_Admin/CreateItem', $createItem->get());
                    $contador++;
                } catch (Exception $e) {
                    // Log de error para producto específico
                    Log::error('Error al enviar producto a WMS', [
                        'producto_id' => $producto->id ?? 'N/A',
                        'error' => $e->getMessage()
                    ]);
                }
            }

            DB::commit();

            Log::info("Productos enviados correctamente", ['total_enviados' => $contador]);

            return response()->json([
                'message' => 'Productos procesados y enviados a WMS.',
                'total_enviados' => $contador
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error general al procesar productos para WMS', ['error' => $e->getMessage()]);

            return response()->json([
                'error' => 'Ocurrió un error al procesar los productos.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
