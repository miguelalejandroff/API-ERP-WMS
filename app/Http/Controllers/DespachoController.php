<?php

namespace App\Http\Controllers;

use App\Models\despachoencab;
use App\Models\despachodetalle;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class DespachoController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $request->validate([
            'numeroDocumento' => 'required',
            'codigoProducto' => 'required',
            'cantidadRecepcionada' => 'required',
            
        ]);

        try {
            despachoencab::where('des_folio', $request->input('numeroDocumento'))
                ->update([
                    'des_estado' => 'A',
                    'des_desestado' => 'AUTORIZADO',
                ]);

            despachodetalle::where([
                'des_folio' => $request->input('numeroDocumento'),
                'des_codigo' => $request->input('codigoProducto')
            ])->update([
                'des_estado' => 'A',
                'des_stockp' => $request->input('cantidadRecepcionada'),
                // Otros campos que necesitas actualizar en despachodetalle
            ]);

            return response()->json(['message' => 'Despacho actualizado correctamente']);
        } catch (Exception $e) {
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
