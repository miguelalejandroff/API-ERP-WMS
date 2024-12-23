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
use Illuminate\Support\Facades\Http;

class AjusteNegativoController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $this->validateRequest($request);

        $fechaRecepcionWMS = Carbon::parse($request->input('fechaRecepcionWMS'))->startOfDay();
        $this->validateFechaRecepcion($fechaRecepcionWMS);

        DB::beginTransaction();

        try {
            $nuevoGuiNumero = $this->generarNuevoGuiNumero($fechaRecepcionWMS);
            $this->procesarGuias($request, $nuevoGuiNumero);
            $this->procesarDetalles($request, $nuevoGuiNumero);

            DB::commit();

            return response()->json(['message' => 'AjusteNegativo actualizado correctamente'], 200);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar desde WMS:', ['error_message' => $e->getMessage()]);
            return response()->json(['message' => 'Error al procesar el ajuste negativo', 'error' => $e->getMessage()], 500);
        }
    }

    private function validateRequest($request)
    {
        $request->validate([
            'numeroDocumento' => 'required',
            'fechaRecepcionWMS' => 'required|date',
            'usuario' => 'required',
            'documentoDetalle' => 'required|array',
            'documentoDetalle.*.codigoProducto' => 'required|string',
            'documentoDetalle.*.BodegaOrigen' => 'required|string',
            'documentoDetalle.*.cantidad' => 'required|numeric',
            'documentoDetalle.*.descripcion' => 'required|string'
        ]);
    }

    private function validateFechaRecepcion($fechaRecepcionWMS)
    {
        $fechaActual = Carbon::now()->startOfDay();

        if ($fechaRecepcionWMS->greaterThan($fechaActual)) {
            Log::error('La fecha de recepción no puede ser futura.', ['fechaRecepcionWMS' => $fechaRecepcionWMS, 'fechaActual' => $fechaActual]);
            throw new Exception('La fecha de recepción no puede ser futura.');
        }
    }

    private function generarNuevoGuiNumero($fechaRecepcionWMS)
    {
        $añoActual = $fechaRecepcionWMS->format('y');
        $mesActual = $fechaRecepcionWMS->format('m');

        $ultimoGuiNumero = cmguias::whereIn('gui_tipgui', ['02', '10'])
            ->whereYear('gui_fechag', $fechaRecepcionWMS->year)
            ->whereMonth('gui_fechag', $fechaRecepcionWMS->month)
            ->where(DB::raw("CAST(SUBSTRING(gui_numero FROM 1 FOR 4) AS INTEGER)"), '=', intval($añoActual . $mesActual))
            ->max('gui_numero');

        $correlativoSecuencial = $ultimoGuiNumero ? intval(substr($ultimoGuiNumero, -3)) + 1 : 1;

        return $añoActual . $mesActual . sprintf("%03d", $correlativoSecuencial);
    }

    private function procesarGuias($request, $nuevoGuiNumero)
    {
        $primerDetalle = $request->json('documentoDetalle')[0];
        $bodegaOrigen = $primerDetalle['BodegaOrigen'];
        $cmbodega = cmbodega::where('bod_codigo', $bodegaOrigen)->first();

        cmguias::updateOrCreate([
            'gui_numero' => $nuevoGuiNumero,
            'gui_guipro' => $request->json('numeroDocumento'),
            'gui_fechag' => Carbon::parse($request->json('fechaRecepcionWMS'))->format('Y-m-d'),
            'gui_tipgui' => "10",
            'gui_sucori' => $cmbodega->bod_codsuc,
            'gui_sucdes' => "0",
            'gui_paract' => "N",
            'gui_fecmod' => Carbon::now()->format('Y-m-d'),
            'gui_codusu' => $request->json('usuario'),
            'gui_empres' => 1
        ]);
    }

    private function procesarDetalles($request, $nuevoGuiNumero)
    {
        foreach ($request->json('documentoDetalle') as $detalle) {
            $producto = cmdetgui::whereRaw('LOWER(gui_produc) = ?', [strtolower($detalle['codigoProducto'])])->first();

            if (!$producto) {
                throw new Exception('El Producto código ' . $detalle['codigoProducto'] . ' no existe.');
            }

            $gui_preuni = $producto->cmproductos->pro_cosmed;

            cmdetgui::updateOrInsert([
                'gui_numero' => $nuevoGuiNumero,
                'gui_produc' => $detalle['codigoProducto'],
                'gui_bodori' => $detalle['BodegaOrigen'],
                'gui_boddes' => "0",
                'gui_tipgui' => "10",
                'gui_descri' => $detalle['descripcion'],
                'gui_canord' => "0",
                'gui_canrep' => abs($detalle['cantidad']),
                'gui_preuni' => $gui_preuni
            ]);
        }
    }

    public function enviaOrdenSalidaWms($document = [])
    {
        try {
            $url = url('/WMS/CreateOrdenEntrada');
            $response = Http::post($url, $document);

            if ($response->failed()) {
                $statusCode = $response->status();
                throw new Exception('Error al enviar la orden a WMS: ' . $response->body());
            }

            Log::info('Orden enviada exitosamente a WMS', ['response' => $response->body()]);
            return $response->body();
        } catch (Exception $e) {
            Log::error('Error al enviar la orden a WMS:', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
