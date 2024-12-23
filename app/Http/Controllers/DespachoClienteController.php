<?php

namespace App\Http\Controllers;

use App\Models\despachoencab;
use App\Models\despachodetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DespachoClienteController extends Controller
{
    /**
     * Actualiza información del despacho desde WMS.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarDesdeWMS(Request $request)
    {
        // Validar los datos usando un FormRequest o inline
        $validated = $request->validate([
            'numeroDocumento' => 'required|string',
            'codigoProducto' => 'required|string',
            'usuario' => 'required|string',
            'cantidadRecepcionada' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            Log::info("Inicio de actualización desde WMS", ['request' => $validated]);

            // Actualizar detalle del despacho
            $this->actualizarDetalleDespacho($validated);

            // Verificar si todos los estados están autorizados
            if ($this->verificarTodosEstadoA($validated['numeroDocumento'])) {
                $this->actualizarEncabezadoDespacho($validated['numeroDocumento']);
            }

            DB::commit();
            Log::info("Actualización desde WMS completada con éxito");

            return response()->json(['message' => 'Despacho actualizado correctamente'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en actualización desde WMS', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Ocurrió un error al actualizar el despacho'], 500);
        }
    }

    /**
     * Actualiza el detalle del despacho.
     *
     * @param array $data
     */
    private function actualizarDetalleDespacho(array $data)
    {
        despachodetalle::where([
            'des_folio' => $data['numeroDocumento'],
            'des_codigo' => $data['codigoProducto']
        ])->update([
            'des_estado' => 'A',
            'des_stockp' => $data['cantidadRecepcionada'],
            'des_usuaut' => $data['usuario']
        ]);
    }

    /**
     * Verifica si todos los detalles del despacho tienen estado 'A'.
     *
     * @param string $numeroDocumento
     * @return bool
     */
    private function verificarTodosEstadoA(string $numeroDocumento): bool
    {
        return despachodetalle::where('des_folio', $numeroDocumento)
            ->where('des_estado', '!=', 'A')
            ->count() === 0;
    }

    /**
     * Actualiza el encabezado del despacho cuando todos los detalles están autorizados.
     *
     * @param string $numeroDocumento
     */
    private function actualizarEncabezadoDespacho(string $numeroDocumento)
    {
        despachoencab::where('des_folio', $numeroDocumento)
            ->update([
                'des_estado' => 'A',
                'des_desestado' => 'AUTORIZADO',
            ]);
    }
}
