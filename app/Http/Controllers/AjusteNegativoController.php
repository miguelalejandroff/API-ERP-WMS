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
use Illuminate\Support\Facades\Http;

class AjusteNegativoController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $request->validate([
            'numeroDocumento'  => 'required',
            'fechaRecepcionWMS'  => 'required|date',
            'usuario'  => 'required',
            'documentoDetalle' => 'required|array',
            'documentoDetalle.*.codigoProducto' => 'required|string',
            'documentoDetalle.*.BodegaOrigen' => 'required|numeric',
            'documentoDetalle.*.cantidad' => 'required|numeric',
            'documentoDetalle.*.descripcion' => 'required|string'
        ]);

        // Definir la variable 
        $fechaRecepcionWMS = Carbon::parse($request->input('fechaRecepcionWMS'))->startOfDay();
        $fechaActual = Carbon::now()->startOfDay();

        Log::info('Fecha de recepción WMS:', ['fechaRecepcionWMS' => $fechaRecepcionWMS]);
        Log::info('Fecha actual:', ['fechaActual' => $fechaActual]);

        // Validar que la fecha no sea futura
        if ($fechaRecepcionWMS->greaterThan($fechaActual)) {
            Log::error('La fecha de recepción no puede ser futura.', ['fechaRecepcionWMS' => $fechaRecepcionWMS, 'fechaActual' => $fechaActual]);
            return response()->json(['message' => 'La fecha de recepción no puede ser futura.'], 422);
        }

        DB::beginTransaction();

        try {
            // Obtener el primer elemento del arreglo documentoDetalle
            $primerDetalle = $request->json('documentoDetalle')[0];

            // Obtener la fecha del JSON en lugar de la fecha actual de la aplicación Laravel
            $fechaRecepcionWMS = $request->json('fechaRecepcionWMS');
            $fechaActual = Carbon::parse($fechaRecepcionWMS);
            $añoActual = $fechaActual->format('y'); // Obtener el año
            $mesActual = $fechaActual->format('m'); // Obtener el mes

            // Obtener el último valor de gui_numero en la tabla cmguias con gui_tipgui 02 o 10
            // Obtener el último valor de gui_numero en la tabla cmguias para el año y mes proporcionados en el JSON
            $ultimoGuiNumero = cmguias::whereIn('gui_tipgui', ['02', '10'])
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

            // Obtener el valor de BodegaOrigen del primer detalle
            $bodegaOrigen = $primerDetalle['BodegaOrigen'];
            $gui_fechag = Carbon::parse($request->json('fechaRecepcionWMS'))->format('Y-m-d');
            $numeroDocumento = $request->json('numeroDocumento');
            $cmbodega = cmbodega::where('bod_codigo', $bodegaOrigen)->first();

            cmguias::updateOrCreate(
                [
                    'gui_numero' => $nuevoGuiNumero,
                    'gui_guipro' => $numeroDocumento,
                    'gui_fechag' => $gui_fechag,
                    'gui_tipgui' => "10",
                    'gui_sucori' => $cmbodega->bod_codsuc,
                    'gui_sucdes' => "0",
                    'gui_paract' => "N",
                    'gui_fecmod' => $gui_fechag,
                    'gui_codusu' => $request->json('usuario'),
                    'gui_empres' => 1
                ]
            );

            Log::info('Operación de actualización en la tabla cmdetgui realizada con éxito.');

            foreach ($request->json('documentoDetalle') as $detalle) {
                $codigoProducto = strtolower($detalle['codigoProducto']);
                $producto = cmdetgui::whereRaw('LOWER(gui_produc) = ?', [$codigoProducto])->first();
                if (!$producto) {
                    throw new Exception('El Producto código ' . $detalle['codigoProducto'] . ' no existe.');
                }
                $gui_preuni = $producto->cmproductos->pro_cosmed;
                $cantidad = abs($detalle['cantidad']);
                Log::info('Valor de gui_preuni:', ['valor' => $gui_preuni]);
                cmdetgui::updateOrInsert(
                    [
                        'gui_numero' => $nuevoGuiNumero,
                        'gui_produc' => $detalle['codigoProducto'],
                        'gui_bodori' => $detalle['BodegaOrigen'],
                        'gui_boddes' => "0",
                        'gui_tipgui' => "10",
                        'gui_descri' => $detalle['descripcion'],
                        'gui_canord' => "0",
                        'gui_canrep' => $cantidad,
                        'gui_preuni' => $gui_preuni
                    ]
                );
            }

            // Actualizar la tabla cmdetinv en masa
            DB::commit();

            return response()->json(['message' => 'AjusteNegativo actualizado correctamente'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS:', [
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    public function enviaOrdenSalidaWms($document = [])
    {
        try {
            $url = url('/WMS/CreateOrdenEntrada');
            $response = Http::post($url, $document);

            if ($response->failed()) {
                $statusCode = $response->status();
                $errorMessage = 'Error al enviar la orden de salida a WMS: ' . $response->body();
                Log::error($errorMessage, ['status_code' => $statusCode, 'document' => $document]);
                throw new Exception($errorMessage);
            }

            Log::info('Orden de salida enviada exitosamente al WMS', [
                'document' => $document,
                'response' => $response->body()
            ]);

            return $response->body(); // Devuelve la respuesta del WMS
        } catch (Exception $e) {
            Log::error('Error al enviar la orden de salida a WMS:', [
                'error_message' => $e->getMessage(),
                'document' => $document
            ]);

            return null; // Devuelve null en caso de error
        }
    }
}
