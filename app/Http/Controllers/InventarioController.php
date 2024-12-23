<?php

namespace App\Http\Controllers;

use App\Models\cminvent;
use App\Models\cmdetinv;
use App\Models\cmproductos;
use App\Models\cmbodega;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventarioController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $request->validate([
            'numeroDocumento' => 'required',
            'fechaCierre' => 'required',
            'Bodega' => 'required',
            'usuario' => 'required',
            'documentoDetalle' => 'required|array',
        ]);

        DB::beginTransaction();

        try {

            $inv_fechai = Carbon::parse($request->json('fechaCierre'))->format('Y-m-d');
            $numeroDocumento = $request->json('numeroDocumento');
            // Buscar la instancia de cmbodega basada en el parámetro Bodega del JSON enviado
            $cmbodega = cmbodega::where('bod_codigo', $request->json('Bodega'))->first();


            // Actualizar o insertar en cminvent
            cminvent::updateOrCreate(
                ['inv_numgui' => $numeroDocumento],
                [
                    'inv_bodega' => $request->json('Bodega'),
                    'inv_fechai' => $inv_fechai,
                    'inv_codusu' => $request->json('usuario'),
                    'inv_sucurs' => $cmbodega->bod_codsuc,
                    'inv_empres' => 1,
                ]
            );

            // Actualizar o insertar en cmdetinv para cada elemento de documentoDetalle
            foreach ($request->json('documentoDetalle') as $detalle) {
                // Obtener el detalle del producto dentro del bucle foreach
                $cmdetgui = cmproductos::where('pro_codigo', $detalle['codigoProducto'])
                ->where('pro_anomes', Carbon::now()->format('Ym'))
                ->first();
                
                // Verificar si se encontró el detalle del producto
                if ($cmdetgui) {
                    // Actualizar o insertar en cmdetinv para cada elemento de documentoDetalle
                    cmdetinv::updateOrInsert( 
                        [
                            'inv_numgui' => $numeroDocumento,
                            'inv_produc' => $detalle['codigoProducto'],
                        ],
                        [
                            'inv_descri' => $cmdetgui->pro_descri,
                            'inv_cantid' => $detalle['cantidad'],
                        ]
                    );
                } else {
                    // Manejar el caso donde no se encuentra el detalle del producto
                    Log::warning('Detalle de producto no encontrado para el código: ' . $detalle['codigoProducto']);
                }
            }
            

            DB::commit();

            return response()->json(['message' => 'Inventario actualizado correctamente']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            Log::error('Error al actualizar desde WMS:', ['stack_trace' => $e->getTrace()]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
        
    }
}
