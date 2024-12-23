<?php

namespace App\Http\Controllers;

use App\Models\pedidosdetalles;
use App\Models\pedidosencabezado;
use App\WMS\Adapters\OrdenSalida\Pedidos;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidosEstadoController extends Controller
{
    public  function actualizarDesdeWMS(Request $request)
    {

        try {

            DB::beginTransaction();

            $solicitudPedido = $request->input('solicitudPedido');
            $pedidosQuery = floor(substr($solicitudPedido, 0, -1));
            $ultimoDigito = substr($solicitudPedido, -1);
            $model = pedidosencabezado::SolicitudPedido($pedidosQuery)->first();

            $pedidos = new Pedidos($model, $ultimoDigito);
            $nroReferencia = $pedidos->nroReferencia($model);

            pedidosdetalles::where([
                'ped_folio' => $nroReferencia,
                'ped_codrub' => $ultimoDigito,
                'ped_estped' => 'A',
            ])->update([
            'ped_estped' => 'M',
            'ped_nomestado' => 'EN MATRIZ',
                // Otros campos que necesitas actualizar en despachodetalle
            ]);

            DB::commit();

            return response()->json(['message' => 'Estado de Pedido actualizado correctamente']);
        } catch (Exception $e) {
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}