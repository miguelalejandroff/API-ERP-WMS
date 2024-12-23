<?php

namespace App\Http\Controllers;

use App\Models\pedidosdetalles;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidosController extends Controller
{
    /**
     * Actualiza el estado de un pedido desde WMS.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarDesdeWMS(Request $request)
    {
        // Validación de datos de entrada
        $validated = $request->validate([
            'numeroDocumento' => 'required|string',
            'tipoDocumentoERP' => 'required|string',
            'numeroOrdenSalida' => 'required|string',
            'usuario' => 'required|string',
            'codigoProducto' => 'required|string',
            'cantidadRecepcionada' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();

            // Buscar el registro específico en pedidosdetalles
            $detallePedido = pedidosdetalles::where([
                'ped_folio' => $validated['numeroDocumento'],
                'ped_codigo' => $validated['codigoProducto'],
                'ped_estped' => 'M' // Estado "M" = en proceso (ejemplo)
            ])->first();

            if (!$detallePedido) {
                Log::warning("Pedido no encontrado o no está en estado 'M'", $validated);
                return response()->json(['message' => 'Pedido no encontrado o ya cerrado.'], 404);
            }

            // Construir el nuevo mensaje
            $nuevoMensaje = $this->construirMensajeLog($detallePedido->ped_msglog, $validated['numeroOrdenSalida']);

            // Actualizar el registro en la base de datos
            $detallePedido->update([
                'ped_estped' => 'C',  // Estado "C" = cerrado (ejemplo)
                'ped_nomestado' => 'CERRADO',
                'ped_cantsol' => $validated['cantidadRecepcionada'],
                'ped_msglog' => $nuevoMensaje,
                'ped_usuaut' => $validated['usuario']
            ]);

            DB::commit();

            Log::info("Pedido actualizado correctamente", ['numeroDocumento' => $validated['numeroDocumento']]);
            return response()->json(['message' => 'Estado de Pedido actualizado correctamente']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS', ['error_message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al actualizar el pedido.'], 500);
        }
    }

    /**
     * Construye el mensaje de log, asegurando longitud y formato correcto.
     *
     * @param string|null $mensajeActual
     * @param string $numeroOrdenSalida
     * @return string
     */
    private function construirMensajeLog(?string $mensajeActual, string $numeroOrdenSalida): string
    {
        $mensajeActual = $mensajeActual ?? '';
        $longitudMensaje = strlen($mensajeActual);

        // Completar con espacios hasta el carácter 175
        $espaciosEnBlanco = str_repeat(' ', max(0, 175 - $longitudMensaje));

        // Construir el mensaje final y truncar a 200 caracteres
        return substr($mensajeActual . $espaciosEnBlanco . 'OSWMS:' . $numeroOrdenSalida, 0, 200);
    }
}
