<?php

namespace App\Http\Controllers;

use App\Models\pedidosdetalles;
use App\Models\pedidosencabezado;
use App\WMS\Adapters\OrdenSalida\Pedidos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class PedidosEstadoController extends Controller
{
    /**
     * Actualiza el estado de un pedido desde WMS.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarDesdeWMS(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'solicitudPedido' => 'required|string|regex:/^\d+$/',
        ]);

        try {
            DB::beginTransaction();

            // Procesar el número de solicitud
            $solicitudPedido = $validated['solicitudPedido'];
            [$pedidosQuery, $ultimoDigito] = $this->extraerPartesSolicitud($solicitudPedido);

            Log::info("Solicitud recibida", [
                'pedidosQuery' => $pedidosQuery,
                'ultimoDigito' => $ultimoDigito
            ]);

            // Buscar el encabezado del pedido
            $model = pedidosencabezado::SolicitudPedido($pedidosQuery)->first();

            if (!$model) {
                Log::warning("No se encontró el encabezado del pedido", ['pedidosQuery' => $pedidosQuery]);
                return response()->json(['message' => 'Encabezado del pedido no encontrado'], 404);
            }

            // Crear instancia de Pedidos y obtener número de referencia
            $pedidos = new Pedidos($model, $ultimoDigito);
            $nroReferencia = $pedidos->nroReferencia($model);

            // Actualizar detalles del pedido
            $actualizados = pedidosdetalles::where([
                'ped_folio' => $nroReferencia,
                'ped_codrub' => $ultimoDigito,
                'ped_estped' => 'A',
            ])->update([
                'ped_estped' => 'M',
                'ped_nomestado' => 'EN MATRIZ',
            ]);

            if ($actualizados === 0) {
                Log::warning("No se encontraron registros para actualizar", ['nroReferencia' => $nroReferencia]);
            } else {
                Log::info("Detalles del pedido actualizados correctamente", ['actualizados' => $actualizados]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Estado de Pedido actualizado correctamente',
                'registros_actualizados' => $actualizados,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS', ['error_message' => $e->getMessage()]);

            return response()->json([
                'error' => 'Ocurrió un error al actualizar el pedido',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extrae la parte principal y el último dígito de la solicitud.
     *
     * @param string $solicitudPedido
     * @return array
     */
    private function extraerPartesSolicitud(string $solicitudPedido): array
    {
        $pedidosQuery = floor(substr($solicitudPedido, 0, -1));
        $ultimoDigito = substr($solicitudPedido, -1);

        return [$pedidosQuery, $ultimoDigito];
    }
}
