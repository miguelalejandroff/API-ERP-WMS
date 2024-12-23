<?php

namespace App\Http\Controllers;

use App\Models\despachoencab;
use App\Models\despachodetalle;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class DespachoClienteController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $request->validate([
            'numeroDocumento' => 'required',
            'codigoProducto' => 'required',
            'usuario' => 'required',
            'cantidadRecepcionada' => 'required',
            
        ]);

        try {

            despachodetalle::where([
                'des_folio' => $request->input('numeroDocumento'),
                'des_codigo' => $request->input('codigoProducto')
            ])->update([
                'des_estado' => 'A',
                'des_stockp' => $request->input('cantidadRecepcionada'),
                'des_usuaut' => $request->input('usuario')
                // Otros campos que necesitas actualizar en despachodetalle
            ]);

            $todosEstadoA = despachodetalle::where('des_folio', $request->input('numeroDocumento'))
                ->where('des_estado', '!=', 'A')
                ->count() == 0;

            if ($todosEstadoA) {
                // Actualizar despachoencab si la condición se cumple
                despachoencab::where('des_folio', $request->input('numeroDocumento'))
                    ->update([
                        'des_estado' => 'A',
                        'des_desestado' => 'AUTORIZADO',
                    ]);
            }

            return response()->json(['message' => 'Despacho actualizado correctamente']);
        } catch (Exception $e) {
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
