<?php

namespace App\Http\Controllers;

use App\Models\cmdetgui;
use App\Models\respuestasguiaswms;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Exception;

class RespuestaDespachoController extends Controller
{
    /**
     * Procesa la respuesta del despacho desde WMS.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function procesarRespuesta(Request $request)
    {
        // Validar los datos de entrada
        $validated = $request->validate([
            'numeroDocumento' => 'required|string',
            'fechaRecepcionWMS' => 'required|date',
            'tipoDocumentoERP' => 'required|string',
            'documentoDetalle' => 'required|array',
            'documentoDetalle.*.codigoProducto' => 'required|string',
            'documentoDetalle.*.cantidadSolicitada' => 'required|numeric',
            'documentoDetalle.*.cantidadRecepcionada' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $gui_fechag = Carbon::parse($validated['fechaRecepcionWMS'])->format('Y-m-d H:i:s');
            $numeroDocumento = $validated['numeroDocumento'];
            $tipoDocumentoERP = $validated['tipoDocumentoERP'];
            $procesados = 0;

            // Iterar sobre los detalles
            foreach ($validated['documentoDetalle'] as $detalle) {
                // Verificar si hay diferencias entre cantidad solicitada y recepcionada
                if ($detalle['cantidadSolicitada'] != $detalle['cantidadRecepcionada']) {
                    // Buscar el detalle de la guía
                    $cmdetgui = cmdetgui::where([
                        'gui_numero' => $numeroDocumento,
                        'gui_tipgui' => $tipoDocumentoERP,
                        'gui_produc' => $detalle['codigoProducto'],
                    ])->first();

                    if (!$cmdetgui) {
                        Log::warning("No se encontró detalle de guía", [
                            'numeroDocumento' => $numeroDocumento,
                            'codigoProducto' => $detalle['codigoProducto']
                        ]);
                        continue; // Saltar al siguiente detalle si no se encuentra
                    }

                    // Actualizar o insertar diferencias
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
                            'gui_estado' => 'A',
                        ]
                    );

                    $procesados++;
                }
            }

            DB::commit();

            Log::info("Diferencias procesadas correctamente", [
                'numeroDocumento' => $numeroDocumento,
                'registros_procesados' => $procesados
            ]);

            return response()->json([
                'message' => 'Diferencias actualizadas correctamente',
                'registros_procesados' => $procesados
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al procesar respuesta de WMS', ['error_message' => $e->getMessage()]);

            return response()->json([
                'error' => 'Error al procesar respuesta de despacho.',
                'details' => $e->getMessage()
            ], 500);
        }
    }
}
