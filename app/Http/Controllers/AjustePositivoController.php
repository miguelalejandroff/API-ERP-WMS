<?php

namespace App\Http\Controllers;

use App\Models\cmguias;
use App\Models\cmdetgui;
use App\Models\cmbodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Exception;

class AjustePositivoController extends Controller
{
    public function actualizarDesdeWMS(Request $request)
    {
        $this->validateRequest($request);

        $fechaRecepcionWMS = Carbon::parse($request->input('fechaRecepcionWMS'))->startOfDay();
        $this->validateFechaRecepcion($fechaRecepcionWMS);

        DB::beginTransaction();

        try {
            $nuevoGuiNumero = $this->generarNuevoGuiNumero($fechaRecepcionWMS);
            $this->guardarGuias($request, $nuevoGuiNumero);
            $this->guardarDetalles($request, $nuevoGuiNumero);

            DB::commit();
            $this->enviaOrdenSalidaWms(['ajuste' => $nuevoGuiNumero . '02']);

            return response()->json(['message' => 'AjustePositivo actualizado correctamente']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error al actualizar ajuste positivo:', [
                'error_message' => $e->getMessage(),
                'request_data' => $request->all()
            ]);
            return response()->json(['message' => 'Error al procesar el ajuste positivo', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Valida la estructura de la solicitud.
     */
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

    /**
     * Valida que la fecha de recepción no sea futura.
     */
    private function validateFechaRecepcion($fechaRecepcionWMS)
    {
        $fechaActual = Carbon::now()->startOfDay();
        if ($fechaRecepcionWMS->greaterThan($fechaActual)) {
            throw new Exception('La fecha de recepción no puede ser futura.');
        }
    }

    /**
     * Genera un nuevo número de guía correlativo.
     */
    private function generarNuevoGuiNumero($fechaRecepcionWMS)
    {
        $añoActual = $fechaRecepcionWMS->format('y');
        $mesActual = $fechaRecepcionWMS->format('m');

        $ultimoGuiNumero = cmguias::whereIn('gui_tipgui', ['02', '10'])
            ->whereYear('gui_fechag', $fechaRecepcionWMS->year)
            ->whereMonth('gui_fechag', $fechaRecepcionWMS->month)
            ->max('gui_numero');

        $correlativoSecuencial = $ultimoGuiNumero && substr($ultimoGuiNumero, 2, 2) == $mesActual
            ? intval(substr($ultimoGuiNumero, -3)) + 1
            : 1;

        return $añoActual . $mesActual . sprintf("%03d", $correlativoSecuencial);
    }

    /**
     * Guarda la guía en la base de datos.
     */
    private function guardarGuias($request, $nuevoGuiNumero)
    {
        $primerDetalle = $request->json('documentoDetalle')[0];
        $bodegaOrigen = $primerDetalle['BodegaOrigen'];
        $cmbodega = cmbodega::where('bod_codigo', $bodegaOrigen)->first();

        cmguias::updateOrCreate([
            'gui_numero' => $nuevoGuiNumero,
            'gui_guipro' => $request->input('numeroDocumento'),
            'gui_fechag' => Carbon::parse($request->input('fechaRecepcionWMS'))->format('Y-m-d'),
            'gui_tipgui' => "02",
            'gui_sucori' => $cmbodega->bod_codsuc,
            'gui_sucdes' => "0",
            'gui_paract' => "N",
            'gui_fecmod' => Carbon::now()->format('Y-m-d'),
            'gui_codusu' => $request->input('usuario'),
            'gui_empres' => 1
        ]);
    }

    /**
     * Guarda los detalles de la guía en la base de datos.
     */
    private function guardarDetalles($request, $nuevoGuiNumero)
    {
        foreach ($request->json('documentoDetalle') as $detalle) {
            $codigoProducto = strtolower($detalle['codigoProducto']);
            $producto = cmdetgui::whereRaw('LOWER(gui_produc) = ?', [$codigoProducto])->first();

            if (!$producto) {
                throw new Exception('El Producto código ' . $detalle['codigoProducto'] . ' no existe.');
            }

            cmdetgui::updateOrInsert([
                'gui_numero' => $nuevoGuiNumero,
                'gui_produc' => $detalle['codigoProducto'],
                'gui_bodori' => $detalle['BodegaOrigen'],
                'gui_boddes' => "0",
                'gui_tipgui' => "02",
                'gui_descri' => $detalle['descripcion'],
                'gui_canord' => "0",
                'gui_canrep' => abs($detalle['cantidad']),
                'gui_preuni' => $producto->cmproductos->pro_cosmed
            ]);
        }
    }

    /**
     * Envía los datos al sistema WMS.
     */
    private function enviaOrdenSalidaWms($document = [])
    {
        try {
            $url = url('/WMS/CreateOrdenEntrada');
            $response = Http::post($url, $document);

            if ($response->failed()) {
                throw new Exception('Error al enviar la orden de salida a WMS: ' . $response->body());
            }

            Log::info('Orden enviada exitosamente a WMS', ['response' => $response->body()]);
            return $response->body();
        } catch (Exception $e) {
            Log::error('Error al enviar la orden de salida a WMS:', ['error_message' => $e->getMessage()]);
            throw $e;
        }
    }
}
