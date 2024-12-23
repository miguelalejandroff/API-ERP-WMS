<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\cmguinum;

class DeleteDespachoEnTransito extends Controller
{
    public function eliminarRegistro(Request $request)
    {
        try {
            // Validar datos de entrada
            $request->validate([
                'numeroDocumento' => 'required|integer',
                'fechaRecepcionWMS' => 'required|date',
            ]);

            $numeroDocumento = $request->input('numeroDocumento');
            $fechaRecepcionWMS = Carbon::parse($request->input('fechaRecepcionWMS'))->format('Y-m-d');

            Log::info("Intentando eliminar registro con número de documento: $numeroDocumento y fecha: $fechaRecepcionWMS");

            // Buscar el registro en la base de datos
            $registro = cmguinum::where('gui_numero', $numeroDocumento)->first();

            if (!$registro) {
                Log::warning("El registro con número de documento $numeroDocumento no existe.");
                return response()->json(['message' => 'El registro no existe'], 404);
            }

            // Registrar los detalles del registro antes de eliminar
            Log::info("Registro encontrado: ", $registro->toArray());

            // Intentar eliminar el registro
            if ($registro->delete()) {
                Log::info("Registro con número de documento $numeroDocumento eliminado correctamente.");
                return response()->json(['message' => 'Registro eliminado correctamente']);
            }

            // Si no se elimina correctamente
            Log::error("Error desconocido al intentar eliminar el registro con número de documento $numeroDocumento.");
            return response()->json(['message' => 'Error al intentar eliminar el registro'], 500);
        } catch (\Illuminate\Database\QueryException $e) {
            // Capturar errores de la base de datos
            Log::error('Error en consulta SQL: ' . $e->getMessage());
            return response()->json(['message' => 'Error en la consulta SQL'], 500);
        } catch (\Exception $e) {
            // Capturar errores generales
            Log::error('Error al eliminar el registro:', [
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Error interno del servidor'], 500);
        }
    }
}
