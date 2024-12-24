<?php

namespace App\Http\Controllers;

use App\Models\cmdetgui;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;
use App\Models\respuestasguiaswms;

class RespuestaDespachoController extends Controller
{
    public function procesarRespuesta(Request $request)
    {
        $request->validate([
            'numeroDocumento' => 'required',
            'fechaRecepcionWMS' => 'required',
            'tipoDocumentoERP' => 'required',
            'documentoDetalle' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $gui_fechag = Carbon::parse($request->json('fechaCierre'))->format('Y-m-d H:i:s');
            $numeroDocumento = $request->json('numeroDocumento');
            $tipoDocumentoERP = $request->json('tipoDocumentoERP');

            foreach ($request->json('documentoDetalle') as $detalle) {
                if ($detalle['cantidadSolicitada'] != $detalle['cantidadRecepcionada']) {
                    $cmdetgui = cmdetgui::where('gui_numero', $numeroDocumento)
                        ->where('gui_tipgui', $tipoDocumentoERP)
                        ->where('gui_produc', $detalle['codigoProducto'])
                        ->first();

                    // Actualizar o insertar en cminvent

                    respuestasguiaswms::updateOrInsert(
                        [
                            'gui_numero' => $numeroDocumento,
                            'gui_produc' => $detalle['codigoProducto'],
                        ],
                        [
                            'gui_tipgui' => $tipoDocumentoERP,
                            'gui_bodori' => $cmdetgui->gui_bodori,
                            'gui_boddes' => $cmdetgui->gui_boddes,

                            'gui_descri' => $cmdetgui->gui_descri,
                            'gui_fechag' => $gui_fechag,
                            'gui_canord' => $detalle['cantidadSolicitada'],
                            'gui_canrep' => $detalle['cantidadRecepcionada'],
                            'gui_saldos' => $detalle['cantidadSolicitada'] - $detalle['cantidadRecepcionada'],
                            'gui_estado' => 'A'
                        ]
                    );
                }
            }

            DB::commit();

            Log::info("Proceso completado", [
                'numeroDocumento' => $numeroDocumento,
                'registros_procesados' => $procesados,
                'registros_no_encontrados' => $noEncontrados
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Proceso completado.',
                'data' => [
                    'registros_procesados' => $procesados,
                    'registros_no_encontrados' => $noEncontrados,
                ],
            ], 200);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Datos de entrada inválidos.',
                'details' => $e->errors(),
            ], 422);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::error('Error de base de datos', ['error_message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Error al interactuar con la base de datos.',
                'details' => $e->getMessage(),
            ], 500);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error general', ['error_message' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => 'Ocurrió un error inesperado.',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}
