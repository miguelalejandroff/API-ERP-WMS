<?php

namespace App\Http\Controllers;

use App\Models\despachoencab;
use App\Models\despachodetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class DespachoController extends Controller
{
    /**
     * Actualiza el despacho desde WMS.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarDesdeWMS(Request $request)
    {
        $validated = $this->validateRequest($request);

        DB::beginTransaction();

        try {
            // Actualizar encabezado de despacho
            $this->actualizarEncabezadoDespacho($validated['numeroDocumento']);

            // Actualizar detalle del despacho
            $this->actualizarDetalleDespacho($validated);

            DB::commit();

            Log::info('Despacho actualizado correctamente', ['numeroDocumento' => $validated['numeroDocumento']]);
            return response()->json(['message' => 'Despacho actualizado correctamente'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar el despacho desde WMS', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json(['error' => 'Ocurrió un error al actualizar el despacho'], 500);
        }
    }

    /**
     * Valida los datos de la petición.
     *
     * @param Request $request
     * @return array
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'numeroDocumento' => 'required|string',
            'codigoProducto' => 'required|string',
            'cantidadRecepcionada' => 'required|numeric',
        ]);
    }

    /**
     * Actualiza el encabezado del despacho.
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

        Log::info("Encabezado de despacho actualizado", ['numeroDocumento' => $numeroDocumento]);
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
        ]);

        Log::info("Detalle de despacho actualizado", [
            'numeroDocumento' => $data['numeroDocumento'],
            'codigoProducto' => $data['codigoProducto'],
            'cantidadRecepcionada' => $data['cantidadRecepcionada']
        ]);
    }
}
