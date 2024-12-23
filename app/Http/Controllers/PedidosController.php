<?php

namespace App\Http\Controllers;

use App\Models\pedidosdetalles;
use App\Models\pedidosencabezado;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PedidosController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $request->validate([
            'numeroDocumento' => 'required',
            'tipoDocumentoERP' => 'required',
            'numeroOrdenSalida' => 'required',
            'usuario' => 'required'
        ]);

        try {

            DB::beginTransaction();
            /* pedidosencabezado::where([
                'ped_folio' => $request->input('numeroDocumento'),
                'ped_estped' => 'M',
                ])->update([
                    'ped_estado' => 'C',
                    'ped_desestado' => 'CERRADO',
                    // Otros campos que necesitas actualizar en despachoencab
                ]);
            
            */

            $numeroOrdenSalida = $request->input('numeroOrdenSalida');

            // Obtener el mensaje actual en ped_msglog
            $mensajeActual = pedidosdetalles::where([
                'ped_folio' => $request->input('numeroDocumento'),
                'ped_codigo' => $request->input('codigoProducto'),
                'ped_estped' => 'M',
            ])->value('ped_msglog');
            
            // Calcular la longitud del mensaje actual
            $longitudMensajeActual = strlen($mensajeActual);
            
            // Completar con espacios en blanco hasta el carácter 175
            $espaciosEnBlanco = str_repeat(' ', max(0, 175 - $longitudMensajeActual));
            
            // Construir la cadena deseada a partir del carácter 175
            $mensaje = substr($mensajeActual . $espaciosEnBlanco . 'OSWMS:' . $numeroOrdenSalida, 0, 200);
            
            // Actualizar la base de datos
            pedidosdetalles::where([
                'ped_folio' => $request->input('numeroDocumento'),
                'ped_codigo' => $request->input('codigoProducto'),
                'ped_estped' => 'M',
            ])->update([
                'ped_estped' => 'C',
                'ped_nomestado' => 'CERRADO',
                'ped_cantsol' => $request->input('cantidadRecepcionada'),
                'ped_msglog' => $mensaje,
                'ped_usuaut' => $request->input('usuario')
            ]);
            

            DB::commit();

            return response()->json(['message' => 'Estado de Pedido actualizado correctamente']);
        } catch (Exception $e) {
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
