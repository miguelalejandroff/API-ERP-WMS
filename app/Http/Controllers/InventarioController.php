<?php

namespace App\Http\Controllers;

use App\Models\cminvent;
use App\Models\cmdetinv;
use App\Models\cmproductos;
use App\Models\cmbodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarioController extends Controller
{
    /**
     * Actualiza el inventario desde WMS.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizarDesdeWMS(Request $request)
    {
        // Validación de la petición
        $validated = $this->validateRequest($request);

        DB::beginTransaction();

        try {
            $invFechaCierre = Carbon::parse($validated['fechaCierre'])->format('Y-m-d');
            $numeroDocumento = $validated['numeroDocumento'];

            // Obtener bodega y validar si existe
            $bodega = cmbodega::where('bod_codigo', $validated['Bodega'])->firstOrFail();

            // Actualizar o insertar en cminvent
            $this->actualizarInventarioPrincipal($validated, $bodega, $invFechaCierre, $numeroDocumento);

            // Procesar detalle del inventario
            $this->procesarDetalleInventario($validated['documentoDetalle'], $numeroDocumento);

            DB::commit();

            Log::info('Inventario actualizado correctamente', ['numeroDocumento' => $numeroDocumento]);
            return response()->json(['message' => 'Inventario actualizado correctamente']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar inventario desde WMS', [
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTrace()
            ]);

            return response()->json(['error' => 'Error al actualizar el inventario: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Valida la petición entrante.
     *
     * @param Request $request
     * @return array
     */
    private function validateRequest(Request $request): array
    {
        return $request->validate([
            'numeroDocumento' => 'required|string',
            'fechaCierre' => 'required|date',
            'Bodega' => 'required|string',
            'usuario' => 'required|string',
            'documentoDetalle' => 'required|array',
            'documentoDetalle.*.codigoProducto' => 'required|string',
            'documentoDetalle.*.cantidad' => 'required|numeric|min:1',
        ]);
    }

    /**
     * Actualiza el inventario principal (tabla cminvent).
     *
     * @param array $data
     * @param object $bodega
     * @param string $fechaCierre
     * @param string $numeroDocumento
     */
    private function actualizarInventarioPrincipal(array $data, $bodega, string $fechaCierre, string $numeroDocumento)
    {
        cminvent::updateOrCreate(
            ['inv_numgui' => $numeroDocumento],
            [
                'inv_bodega' => $data['Bodega'],
                'inv_fechai' => $fechaCierre,
                'inv_codusu' => $data['usuario'],
                'inv_sucurs' => $bodega->bod_codsuc,
                'inv_empres' => 1,
            ]
        );

        Log::info('Inventario principal actualizado', ['numeroDocumento' => $numeroDocumento]);
    }

    /**
     * Procesa y actualiza el detalle del inventario (tabla cmdetinv).
     *
     * @param array $detalles
     * @param string $numeroDocumento
     */
    private function procesarDetalleInventario(array $detalles, string $numeroDocumento)
    {
        // Obtener todos los productos necesarios en una sola consulta
        $codigosProductos = collect($detalles)->pluck('codigoProducto')->unique();
        $productos = cmproductos::whereIn('pro_codigo', $codigosProductos)
            ->where('pro_anomes', Carbon::now()->format('Ym'))
            ->get()
            ->keyBy('pro_codigo');

        // Procesar cada detalle
        foreach ($detalles as $detalle) {
            $producto = $productos[$detalle['codigoProducto']] ?? null;

            if ($producto) {
                cmdetinv::updateOrInsert(
                    ['inv_numgui' => $numeroDocumento, 'inv_produc' => $detalle['codigoProducto']],
                    [
                        'inv_descri' => $producto->pro_descri,
                        'inv_cantid' => $detalle['cantidad'],
                    ]
                );
            } else {
                Log::warning("Producto no encontrado en catálogo", ['codigoProducto' => $detalle['codigoProducto']]);
            }
        }

        Log::info('Detalle del inventario procesado', ['numeroDocumento' => $numeroDocumento]);
    }
}
