<?php

namespace App\Http\Controllers;

use App\Models\cmguias;
use App\Models\cmdetgui;
use App\Models\cmbodega;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TraspasoBodegaController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $request->validate([
            'numeroDocumento'  => 'required',
            'fechaRecepcionWMS'  => 'required',
            'usuario'  => 'required',
            'documentoDetalle' => 'required|array'
        ]);

        DB::beginTransaction();

        try {

            // Obtener la fecha del JSON en lugar de la fecha actual de la aplicación Laravel
            $fechaRecepcionWMS = $request->json('fechaRecepcionWMS');
            $fechaActual = Carbon::parse($fechaRecepcionWMS);
            $añoActual = $fechaActual->format('y'); // Obtener el año
            $mesActual = $fechaActual->format('m'); // Obtener el mes

            // Obtener el último valor de gui_numero en la tabla cmguias con gui_tipgui 02 o 10
            // Obtener el último valor de gui_numero en la tabla cmguias para el año y mes proporcionados en el JSON
            $ultimoGuiNumero = cmguias::whereIn('gui_tipgui', ['27', '28', '29'])
                ->whereYear('gui_fechag', $fechaActual->year)
                ->whereMonth('gui_fechag', $fechaActual->month)
                ->where(DB::raw("CAST(SUBSTRING(gui_numero FROM 1 FOR 4) AS INTEGER)"), '=', intval($añoActual . $mesActual))
                ->max('gui_numero');


            // Log del último número obtenido de la base de datos
            Log::info('Ultimo valor de gui_numero obtenido de la base de datos: ' . $ultimoGuiNumero);

            // Inicializar el correlativo secuencial
            $correlativoSecuencial = 1;

            // Si no hay ningún número anterior, comenzar desde 1
            if ($ultimoGuiNumero !== null) {
                // Obtener el mes del último número
                $mesUltimoNumero = substr($ultimoGuiNumero, 2, 2);

                // Verificar si el último número pertenece al mes actual
                if ($mesActual == $mesUltimoNumero) {
                    // Si el último número pertenece al mes actual, incrementar el correlativo en 1
                    $correlativoSecuencial = intval(substr($ultimoGuiNumero, -3)) + 1;
                }
            }

            // Combinar los componentes para formar el número completo
            $nuevoGuiNumero = $añoActual . $mesActual . sprintf("%03d", $correlativoSecuencial);

            Log::info('Nuevo valor de gui_numero: ' . $nuevoGuiNumero);

            foreach ($request->json('documentoDetalle') as $detalle) {
                $gui_fechag = Carbon::parse($request->json('fechaRecepcionWMS'))->format('Y-m-d');
                $cmbodega = cmbodega::where('bod_codigo', $detalle['BodegaOrigen'])->first();
                $cmbodegades = cmbodega::where('bod_codigo', $detalle['BodegaDestino'])->first();
                $gui_tipgui = $detalle['BodegaOrigen'] == 23 ? 28 : ($detalle['BodegaDestino'] == 23 ? 27 : null);

                cmguias::updateOrCreate(
                    [
                        'gui_numero' => $nuevoGuiNumero,

                        'gui_fechag' => $gui_fechag,
                        'gui_tipgui' => $gui_tipgui,
                        'gui_codusu' => $request->json('usuario'),
                        'gui_sucori' => $cmbodega->bod_codsuc,
                        'gui_sucdes' => $cmbodegades->bod_codsuc,
                        'gui_paract' => "N",
                        'gui_empres' => 1

                        // Otros campos según la lógica proporcionada
                    ]
                );
            }


            Log::info('Operación de actualización en la tabla cmdetgui realizada con éxito.');

            foreach ($request->json('documentoDetalle') as $detalle) {

                $gui_tipgui = $detalle['BodegaOrigen'] == 23 ? 28 : ($detalle['BodegaDestino'] == 23 ? 27 : null);
                $codigoProducto = strtolower($detalle['codigoProducto']);
                $producto = cmdetgui::whereRaw('LOWER(gui_produc) = ?', [$codigoProducto])->first();
                if (!$producto) {
                    throw new Exception('El Producto código ' . $detalle['codigoProducto'] . ' no existe.');
                }
                $gui_preuni = $producto->cmproductos->pro_cosmed;
                $cantidad = abs($detalle['cantidad']);
                cmdetgui::create(
                    [
                        'gui_numero' => $nuevoGuiNumero,
                        'gui_produc' => $codigoProducto,
                        'gui_bodori' => $detalle['BodegaOrigen'],
                        'gui_boddes' => $detalle['BodegaDestino'],
                        'gui_tipgui' => $gui_tipgui,
                        'gui_descri' => $detalle['descripcion'],
                        'gui_canord' => "0",
                        'gui_canrep' => $cantidad,
                        'gui_preuni' => $gui_preuni

                        // Otros campos según la lógica proporcionada
                    ]
                );
            }


            // Actualizar la tabla cmdetinv en masa
            DB::commit();

            return response()->json(['message' => 'TraspasoBodega actualizado correctamente']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
