<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\cmguinum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class DeleteDespachoEnTransito extends Controller
{
    /**
     * Elimina un registro por número de documento y fecha de recepción.
     */
    public function eliminarRegistro(Request $request)
    {
        $validated = $request->validate([
            'numeroDocumento' => 'required|integer',
            'fechaRecepcionWMS' => 'required|date|before_or_equal:today',
        ]);

        $numeroDocumento = $validated['numeroDocumento'];
        $fechaRecepcionWMS = Carbon::parse($validated['fechaRecepcionWMS'])->format('Y-m-d');

        Log::info("Eliminando registro", compact('numeroDocumento', 'fechaRecepcionWMS'));

        try {
            $registro = cmguinum::where('gui_numero', $numeroDocumento)->firstOrFail();

            DB::transaction(function () use ($registro) {
                Log::info("Registro encontrado", $registro->toArray());
                $registro->delete();
            });

            Log::info("Registro eliminado correctamente", ['gui_numero' => $numeroDocumento]);
            return response()->json(['message' => 'Registro eliminado correctamente'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::warning("Registro no encontrado", ['gui_numero' => $numeroDocumento]);
            return response()->json(['message' => 'El registro no existe'], 404);
        } catch (\Exception $e) {
            Log::error("Error al eliminar registro", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
