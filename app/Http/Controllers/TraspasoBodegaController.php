<?php

namespace App\Http\Controllers;

use App\Models\cmguias;
use App\Models\cmdetgui;
use App\Models\cmbodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class TraspasoBodegaController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        // Validar la estructura del JSON
        $validated = $request->validate([
            'numeroDocumento' => 'required|string',
            'fechaRecepcionWMS' => 'required|date',
            'usuario' => 'required|string',
            'documentoDetalle' => 'required|array|min:1',
            'documentoDetalle.*.BodegaOrigen' => 'required|numeric',
            'documentoDetalle.*.BodegaDestino' => 'required|numeric',
            'documentoDetalle.*.codigoProducto' => 'required|string',
            'documentoDetalle.*.descripcion' => 'required|string',
            'documentoDetalle.*.cantidad' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $fechaActual = Carbon::parse($validated['fechaRecepcionWMS']);
            $nuevoGuiNumero = $this->generarNuevoGuiNumero($fechaActual);

            // Procesar documentoDetalle una sola vez
            foreach ($validated['documentoDetalle'] as $detalle) {
                $bodegaOrigen = cmbodega::where('bod_codigo', $detalle['BodegaOrigen'])->firstOrFail();
                $bodegaDestino = cmbodega::where('bod_codigo', $detalle['BodegaDestino'])->firstOrFail();
                $gui_tipgui = $this->determinarTipoGuia($detalle['BodegaOrigen'], $detalle['BodegaDestino']);

                // Insertar o actualizar cmguias
                cmguias::updateOrCreate(
                    ['gui_numero' => $nuevoGuiNumero],
                    [
                        'gui_fechag' => $fechaActual->format('Y-m-d'),
                        'gui_tipgui' => $gui_tipgui,
                        'gui_codusu' => $validated['usuario'],
                        'gui_sucori' => $bodegaOrigen->bod_codsuc,
                        'gui_sucdes' => $bodegaDestino->bod_codsuc,
                        'gui_paract' => 'N',
                        'gui_empres' => 1,
                    ]
                );

                // Obtener precio unitario (si existe el producto)
                $precioUnitario = cmdetgui::where('gui_produc', $detalle['codigoProducto'])
                    ->first()
                    ->cmproductos
                    ->pro_cosmed ?? 0;

                // Insertar cmdetgui
                cmdetgui::create([
                    'gui_numero' => $nuevoGuiNumero,
                    'gui_produc' => $detalle['codigoProducto'],
                    'gui_bodori' => $detalle['BodegaOrigen'],
                    'gui_boddes' => $detalle['BodegaDestino'],
                    'gui_tipgui' => $gui_tipgui,
                    'gui_descri' => $detalle['descripcion'],
                    'gui_canord' => 0,
                    'gui_canrep' => $detalle['cantidad'],
                    'gui_preuni' => $precioUnitario,
                ]);
            }

            DB::commit();
            Log::info("Traspaso de bodega completado", ['numeroDocumento' => $validated['numeroDocumento']]);

            return response()->json(['message' => 'Traspaso de bodega actualizado correctamente']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en actualizarDesdeWMS', ['error_message' => $e->getMessage()]);
            return response()->json(['error' => 'Error al procesar la solicitud', 'details' => $e->getMessage()], 500);
        }
    }

    /**
     * Generar un nuevo número de guía basado en fecha.
     */
    private function generarNuevoGuiNumero(Carbon $fechaActual): string
    {
        $añoMes = $fechaActual->format('ym');
        $ultimoNumero = cmguias::whereIn('gui_tipgui', ['27', '28', '29'])
            ->whereYear('gui_fechag', $fechaActual->year)
            ->whereMonth('gui_fechag', $fechaActual->month)
            ->max('gui_numero');

        $correlativo = 1;
        if ($ultimoNumero) {
            $mesUltimo = substr($ultimoNumero, 2, 2);
            $correlativo = ($mesUltimo == $fechaActual->format('m'))
                ? intval(substr($ultimoNumero, -3)) + 1
                : 1;
        }

        return $añoMes . sprintf("%03d", $correlativo);
    }

    /**
     * Determinar el tipo de guía según las bodegas.
     */
    private function determinarTipoGuia($bodegaOrigen, $bodegaDestino): int
    {
        return $bodegaOrigen == 23 ? 28 : ($bodegaDestino == 23 ? 27 : 29);
    }
}
